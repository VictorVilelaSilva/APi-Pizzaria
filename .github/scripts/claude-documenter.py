#!/usr/bin/env python3
"""
Claude-based Documenter Script
Generates Notion documentation following the Documenter chatmode template
using Claude AI to analyze code changes and create structured documentation.
"""

import os
import json
import sys
from anthropic import Anthropic
import httpx
from datetime import datetime

# Configuration
ANTHROPIC_API_KEY = os.getenv('ANTHROPIC_API_KEY')
NOTION_API_KEY = os.getenv('NOTION_API_KEY')
NOTION_PARENT_PAGE_ID = os.getenv('NOTION_PARENT_PAGE_ID')
NOTION_API_VERSION = '2022-06-28'
NOTION_API_BASE = 'https://api.notion.com/v1'

# Load documentation payload
with open('documentation-payload.json', 'r', encoding='utf-8') as f:
    payload = json.load(f)

with open('changes.txt', 'r', encoding='utf-8') as f:
    changes = f.read()

with open('commits.txt', 'r', encoding='utf-8') as f:
    commits = f.read()

# Try to read full diff (might be large)
try:
    with open('full-diff.txt', 'r', encoding='utf-8') as f:
        full_diff = f.read()[:50000]  # Limit to 50k chars
except:
    full_diff = "Diff too large to include"

def create_notion_page(parent_id, title, content_blocks):
    """Create a page in Notion"""
    url = f"{NOTION_API_BASE}/pages"
    headers = {
        'Authorization': f'Bearer {NOTION_API_KEY}',
        'Notion-Version': NOTION_API_VERSION,
        'Content-Type': 'application/json',
    }
    
    data = {
        'parent': {'page_id': parent_id},
        'properties': {
            'title': {
                'title': [
                    {'text': {'content': title}}
                ]
            }
        },
        'children': content_blocks
    }
    
    with httpx.Client(timeout=30.0) as client:
        response = client.post(url, headers=headers, json=data)
        
        if response.status_code != 200:
            print(f"❌ Notion API Error: {response.status_code}")
            print(response.text)
            raise Exception(f"Failed to create Notion page: {response.text}")
        
        return response.json()

def ask_claude_for_analysis():
    """Use Claude to analyze the changes and generate structured documentation"""
    
    client = Anthropic(api_key=ANTHROPIC_API_KEY)
    
    prompt = f"""You are a technical documentation specialist analyzing a merged pull request for a CodeIgniter 4 pizzaria API project.

        # Project Context
        This is a modular CodeIgniter 4 API following these patterns:
        - Modular architecture with Controllers, Services, DTOs, Models
        - RESTful API endpoints
        - Database migrations organized by ticket (PDB folders)
        - Multi-database setup (default, message, accountDigital, oauth)

        # Task Information
        Ticket: {payload['ticket_number']}
        Branch: {payload['branch_name']}
        Description: {payload['short_description']}
        PR Title: {payload['pr_title']}
        Author: {payload['author']}
        Merge Date: {payload['merge_date']}
        Files Changed: {payload['files_changed']}

        # Changed Files
        {changes}

        # Commit History
        {commits}

        # Code Diff (partial)
        {full_diff}

        # Your Task
        Analyze these changes and provide a structured JSON response for documentation following this EXACT format:

        {{
        "overview": {{
            "summary": "Brief 2-3 sentence summary of what was implemented",
            "business_context": "What business problem does this solve?",
            "status": "Completed"
        }},
        "technical_details": {{
            "changes_made": [
            "Bullet point 1 of changes",
            "Bullet point 2 of changes"
            ],
            "database_changes": [
            "Migration details if any, or empty array"
            ],
            "integration_points": [
            "External integrations if any, or empty array"
            ]
        }},
        "endpoints": [
            {{
            "method": "POST",
            "path": "/api/endpoint/path",
            "description": "What this endpoint does",
            "file_location": "app/Modules/ModuleName/Controllers/ControllerName.php",
            "request_params": {{
                "param1": "string - description",
                "param2": "integer - description"
            }},
            "request_headers": {{
                "Authorization": "Bearer <token>",
                "Content-Type": "application/json"
            }},
            "request_example": {{
                "param1": "example value",
                "param2": 123
            }},
            "response_success": {{
                "data": {{}},
                "message": "Success message"
            }},
            "response_error": {{
                "error": "Error message",
                "code": "ERROR_CODE"
            }},
            "business_rules": [
                "Rule 1 description",
                "Rule 2 description"
            ],
            "validations": [
                "Validation 1",
                "Validation 2"
            ]
            }}
        ],
        "deployment_notes": {{
            "environment_variables": [
            "ENV_VAR_NAME - description"
            ],
            "configuration_changes": [
            "Config change description"
            ],
            "migration_steps": [
            "Migration step 1",
            "Migration step 2"
            ],
            "dependencies": [
            "New dependency 1"
            ]
        }}
        }}

        IMPORTANT INSTRUCTIONS:
        1. Analyze the actual code changes - don't make assumptions
        2. Only include endpoints that were actually created or modified
        3. Extract real parameter names from the code
        4. Be specific about file locations
        5. If no database changes, return empty array for database_changes
        6. If no endpoints were modified, return empty array for endpoints
        7. Focus on WHAT WAS DONE, not what will be done
        8. Use Brazilian Portuguese for business context and descriptions
        9. Keep technical terms in English (endpoints, parameters, etc)

        Return ONLY valid JSON, no markdown formatting."""

    message = client.messages.create(
        model="claude-sonnet-4-5-20250929",
        max_tokens=8000,
        messages=[
            {"role": "user", "content": prompt}
        ]
    )
    
    response_text = message.content[0].text
    
    # Remove markdown code blocks if present
    if response_text.startswith('```'):
        response_text = response_text.split('```')[1]
        if response_text.startswith('json'):
            response_text = response_text[4:]
        response_text = response_text.strip()
    
    return json.loads(response_text)

def build_main_page_blocks(analysis):
    """Build Notion blocks for the main task page"""
    blocks = []
    
    # Header
    blocks.append({
        'object': 'block',
        'type': 'heading_1',
        'heading_1': {
            'rich_text': [{'type': 'text', 'text': {'content': '📋 Visão Geral da Task'}}]
        }
    })
    
    # Overview paragraph
    blocks.append({
        'object': 'block',
        'type': 'paragraph',
        'paragraph': {
            'rich_text': [{'type': 'text', 'text': {'content': analysis['overview']['summary']}}]
        }
    })
    
    # Divider
    blocks.append({'object': 'block', 'type': 'divider', 'divider': {}})
    
    # Task Information Callout
    blocks.append({
        'object': 'block',
        'type': 'callout',
        'callout': {
            'rich_text': [
                {'type': 'text', 'text': {'content': f"Ticket: {payload['ticket_number']}\n", 'link': None}, 'annotations': {'bold': True}},
                {'type': 'text', 'text': {'content': f"Developer: {payload['author']}\n"}},
                {'type': 'text', 'text': {'content': f"Data: {datetime.fromisoformat(payload['merge_date'].replace('Z', '+00:00')).strftime('%d/%m/%Y')}\n"}},
                {'type': 'text', 'text': {'content': f"Status: {analysis['overview']['status']}"}}
            ],
            'icon': {'emoji': '📊'}
        }
    })
    
    # Pull Request Link
    blocks.append({
        'object': 'block',
        'type': 'paragraph',
        'paragraph': {
            'rich_text': [
                {'type': 'text', 'text': {'content': '🔗 Pull Request: '}},
                {
                    'type': 'text',
                    'text': {'content': f"#{payload['pr_number']}", 'link': {'url': payload['pr_url']}},
                    'annotations': {'code': True}
                }
            ]
        }
    })
    
    # Business Context
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '💼 Contexto de Negócio'}}]
        }
    })
    
    blocks.append({
        'object': 'block',
        'type': 'paragraph',
        'paragraph': {
            'rich_text': [{'type': 'text', 'text': {'content': analysis['overview']['business_context']}}]
        }
    })
    
    # Divider
    blocks.append({'object': 'block', 'type': 'divider', 'divider': {}})
    
    # Technical Details
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '🔧 Detalhes Técnicos'}}]
        }
    })
    
    # Changes Made
    blocks.append({
        'object': 'block',
        'type': 'heading_3',
        'heading_3': {
            'rich_text': [{'type': 'text', 'text': {'content': 'Alterações Realizadas'}}]
        }
    })
    
    for change in analysis['technical_details']['changes_made']:
        blocks.append({
            'object': 'block',
            'type': 'bulleted_list_item',
            'bulleted_list_item': {
                'rich_text': [{'type': 'text', 'text': {'content': change}}]
            }
        })
    
    # API Endpoints section (if any)
    if analysis['endpoints']:
        blocks.append({
            'object': 'block',
            'type': 'heading_3',
            'heading_3': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Endpoints da API Criados/Modificados'}}]
            }
        })
        
        blocks.append({
            'object': 'block',
            'type': 'paragraph',
            'paragraph': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Esta task envolveu os seguintes endpoints (clique para ver documentação detalhada):'}}]
            }
        })
        
        # We'll add links to sub-pages here later (after creating them)
        # For now, just list the endpoints
        for endpoint in analysis['endpoints']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [
                        {'type': 'text', 'text': {'content': f"{endpoint['method']} ", 'link': None}, 'annotations': {'bold': True, 'code': True}},
                        {'type': 'text', 'text': {'content': endpoint['path'], 'link': None}, 'annotations': {'code': True}}
                    ]
                }
            })
    
    # Database Changes
    if analysis['technical_details']['database_changes']:
        blocks.append({
            'object': 'block',
            'type': 'heading_3',
            'heading_3': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Alterações no Banco de Dados'}}]
            }
        })
        
        for db_change in analysis['technical_details']['database_changes']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': db_change}}]
                }
            })
    
    # Integration Points
    if analysis['technical_details']['integration_points']:
        blocks.append({
            'object': 'block',
            'type': 'heading_3',
            'heading_3': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Pontos de Integração'}}]
            }
        })
        
        for integration in analysis['technical_details']['integration_points']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': integration}}]
                }
            })
    
    # Changed Files (toggle)
    blocks.append({
        'object': 'block',
        'type': 'toggle',
        'toggle': {
            'rich_text': [{'type': 'text', 'text': {'content': f'📁 Ver arquivos modificados ({payload["files_changed"]} arquivos)'}}],
            'children': [
                {
                    'type': 'code',
                    'code': {
                        'rich_text': [{'type': 'text', 'text': {'content': changes[:2000]}}],  # Limit to 2000 chars
                        'language': 'plain text'
                    }
                }
            ]
        }
    })
    
    # Commit History (toggle)
    blocks.append({
        'object': 'block',
        'type': 'toggle',
        'toggle': {
            'rich_text': [{'type': 'text', 'text': {'content': '📝 Ver histórico de commits'}}],
            'children': [
                {
                    'type': 'code',
                    'code': {
                        'rich_text': [{'type': 'text', 'text': {'content': commits[:2000]}}],  # Limit to 2000 chars
                        'language': 'plain text'
                    }
                }
            ]
        }
    })
    
    # Divider
    blocks.append({'object': 'block', 'type': 'divider', 'divider': {}})
    
    # Deployment Notes
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '🚀 Notas de Deploy'}}]
        }
    })
    
    deployment = analysis['deployment_notes']
    
    if deployment['environment_variables']:
        blocks.append({
            'object': 'block',
            'type': 'heading_3',
            'heading_3': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Variáveis de Ambiente'}}]
            }
        })
        for env_var in deployment['environment_variables']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': env_var}, 'annotations': {'code': True}}]
                }
            })
    
    if deployment['configuration_changes']:
        blocks.append({
            'object': 'block',
            'type': 'heading_3',
            'heading_3': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Alterações de Configuração'}}]
            }
        })
        for config in deployment['configuration_changes']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': config}}]
                }
            })
    
    if deployment['migration_steps']:
        blocks.append({
            'object': 'block',
            'type': 'heading_3',
            'heading_3': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Passos de Migração'}}]
            }
        })
        for step in deployment['migration_steps']:
            blocks.append({
                'object': 'block',
                'type': 'numbered_list_item',
                'numbered_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': step}}]
                }
            })
    
    if deployment['dependencies']:
        blocks.append({
            'object': 'block',
            'type': 'heading_3',
            'heading_3': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Dependências'}}]
            }
        })
        for dep in deployment['dependencies']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': dep}}]
                }
            })
    
    # If no deployment notes, add placeholder
    if not any([deployment['environment_variables'], deployment['configuration_changes'], 
                deployment['migration_steps'], deployment['dependencies']]):
        blocks.append({
            'object': 'block',
            'type': 'callout',
            'callout': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Nenhuma configuração especial necessária para deploy.'}}],
                'icon': {'emoji': '✅'}
            }
        })
    
    return blocks

def build_endpoint_page_blocks(endpoint):
    """Build Notion blocks for an endpoint sub-page"""
    blocks = []
    
    # Endpoint header
    blocks.append({
        'object': 'block',
        'type': 'heading_1',
        'heading_1': {
            'rich_text': [
                {'type': 'text', 'text': {'content': f"{endpoint['method']} ", 'link': None}, 'annotations': {'bold': True}},
                {'type': 'text', 'text': {'content': endpoint['path'], 'link': None}, 'annotations': {'code': True}}
            ]
        }
    })
    
    # Description
    blocks.append({
        'object': 'block',
        'type': 'paragraph',
        'paragraph': {
            'rich_text': [{'type': 'text', 'text': {'content': endpoint['description']}}]
        }
    })
    
    # Divider
    blocks.append({'object': 'block', 'type': 'divider', 'divider': {}})
    
    # Route Location
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '📍 Localização da Rota'}}]
        }
    })
    
    blocks.append({
        'object': 'block',
        'type': 'paragraph',
        'paragraph': {
            'rich_text': [
                {'type': 'text', 'text': {'content': 'File: '}},
                {'type': 'text', 'text': {'content': endpoint['file_location'], 'link': None}, 'annotations': {'code': True}}
            ]
        }
    })
    
    # Request Parameters
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '📥 Parâmetros da Requisição'}}]
        }
    })
    
    if endpoint['request_params']:
        param_text = json.dumps(endpoint['request_params'], indent=2, ensure_ascii=False)
        blocks.append({
            'object': 'block',
            'type': 'code',
            'code': {
                'rich_text': [{'type': 'text', 'text': {'content': param_text}}],
                'language': 'json'
            }
        })
    else:
        blocks.append({
            'object': 'block',
            'type': 'paragraph',
            'paragraph': {
                'rich_text': [{'type': 'text', 'text': {'content': 'Nenhum parâmetro requerido.'}}]
            }
        })
    
    # Request Headers
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '📋 Headers da Requisição'}}]
        }
    })
    
    headers_text = json.dumps(endpoint['request_headers'], indent=2, ensure_ascii=False)
    blocks.append({
        'object': 'block',
        'type': 'code',
        'code': {
            'rich_text': [{'type': 'text', 'text': {'content': headers_text}}],
            'language': 'json'
        }
    })
    
    # Request Example
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '📤 Exemplo de Requisição'}}]
        }
    })
    
    request_text = json.dumps(endpoint['request_example'], indent=2, ensure_ascii=False)
    blocks.append({
        'object': 'block',
        'type': 'code',
        'code': {
            'rich_text': [{'type': 'text', 'text': {'content': request_text}}],
            'language': 'json'
        }
    })
    
    # Response Success
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '✅ Resposta de Sucesso (200)'}}]
        }
    })
    
    success_text = json.dumps(endpoint['response_success'], indent=2, ensure_ascii=False)
    blocks.append({
        'object': 'block',
        'type': 'code',
        'code': {
            'rich_text': [{'type': 'text', 'text': {'content': success_text}}],
            'language': 'json'
        }
    })
    
    # Response Error
    blocks.append({
        'object': 'block',
        'type': 'heading_2',
        'heading_2': {
            'rich_text': [{'type': 'text', 'text': {'content': '❌ Resposta de Erro (4xx/5xx)'}}]
        }
    })
    
    error_text = json.dumps(endpoint['response_error'], indent=2, ensure_ascii=False)
    blocks.append({
        'object': 'block',
        'type': 'code',
        'code': {
            'rich_text': [{'type': 'text', 'text': {'content': error_text}}],
            'language': 'json'
        }
    })
    
    # Business Rules
    if endpoint['business_rules']:
        blocks.append({
            'object': 'block',
            'type': 'heading_2',
            'heading_2': {
                'rich_text': [{'type': 'text', 'text': {'content': '💼 Regras de Negócio'}}]
            }
        })
        
        for rule in endpoint['business_rules']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': rule}}]
                }
            })
    
    # Validations
    if endpoint['validations']:
        blocks.append({
            'object': 'block',
            'type': 'heading_2',
            'heading_2': {
                'rich_text': [{'type': 'text', 'text': {'content': '✔️ Validações'}}]
            }
        })
        
        for validation in endpoint['validations']:
            blocks.append({
                'object': 'block',
                'type': 'bulleted_list_item',
                'bulleted_list_item': {
                    'rich_text': [{'type': 'text', 'text': {'content': validation}}]
                }
            })
    
    return blocks

def main():
    print("🚀 Iniciando geração de documentação no Notion...")
    print(f"📝 Ticket: {payload['ticket_number']}")
    print(f"🌿 Branch: {payload['branch_name']}")
    print(f"📊 Arquivos alterados: {payload['files_changed']}")
    print()
    
    # Step 1: Analyze changes with Claude
    print("🤖 Analisando alterações com Claude AI...")
    try:
        analysis = ask_claude_for_analysis()
        print("✅ Análise concluída!")
        print(f"   - Endpoints detectados: {len(analysis['endpoints'])}")
        print(f"   - Alterações de DB: {len(analysis['technical_details']['database_changes'])}")
        print()
    except Exception as e:
        print(f"❌ Erro na análise: {e}")
        sys.exit(1)
    
    # Step 2: Create main task page
    print("📄 Criando página principal da task...")
    main_title = f"Task - {payload['ticket_number']} - {payload['short_description'] or payload['pr_title']}"
    
    try:
        main_blocks = build_main_page_blocks(analysis)
        main_page = create_notion_page(NOTION_PARENT_PAGE_ID, main_title, main_blocks)
        print(f"✅ Página principal criada: {main_page['url']}")
        print()
    except Exception as e:
        print(f"❌ Erro ao criar página principal: {e}")
        sys.exit(1)
    
    # Step 3: Create sub-pages for each endpoint
    endpoint_pages = []
    if analysis['endpoints']:
        print(f"📑 Criando {len(analysis['endpoints'])} sub-páginas de endpoints...")
        for i, endpoint in enumerate(analysis['endpoints'], 1):
            endpoint_title = f"{endpoint['method']} - {endpoint['path']}"
            try:
                endpoint_blocks = build_endpoint_page_blocks(endpoint)
                endpoint_page = create_notion_page(main_page['id'], endpoint_title, endpoint_blocks)
                endpoint_pages.append({
                    'title': endpoint_title,
                    'url': endpoint_page['url']
                })
                print(f"   ✅ [{i}/{len(analysis['endpoints'])}] {endpoint_title}")
            except Exception as e:
                print(f"   ⚠️  Erro ao criar sub-página {endpoint_title}: {e}")
        print()
    
    # Step 4: Summary
    print("✨ Documentação criada com sucesso!")
    print(f"📎 Página Principal: {main_page['url']}")
    
    if endpoint_pages:
        print(f"\n📑 Sub-páginas criadas ({len(endpoint_pages)}):")
        for ep in endpoint_pages:
            print(f"   • {ep['title']}")
            print(f"     {ep['url']}")
    
    # Save main page URL for GitHub Actions
    with open('notion-page-url.txt', 'w') as f:
        f.write(main_page['url'])
    
    print("\n🎉 Processo finalizado!")

if __name__ == '__main__':
    main()
