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
    """Create a page in Notion using HTTPX"""
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
    
    with httpx.Client(timeout=300.0) as client:  # 5 minutes timeout
        response = client.post(url, headers=headers, json=data)
    
    if response.status_code not in [200, 201]:
        print(f"❌ Notion API Error: {response.status_code}")
        print(response.text)
        raise Exception(f"Failed to create Notion page: {response.text}")
    
    return response.json()

def extract_controller_files(changes_text):
    """Extract controller file paths from changes"""
    controller_files = []
    for line in changes_text.split('\n'):
        if 'Controllers/' in line and line.strip().endswith('.php'):
            # Extract just the file path
            parts = line.strip().split()
            if parts:
                file_path = parts[0] if not parts[0].startswith(('M', 'A', 'D')) else parts[1] if len(parts) > 1 else None
                if file_path and 'Controllers/' in file_path:
                    controller_files.append(file_path)
    return controller_files

def extract_migration_files(changes_text):
    """Extract migration file paths from changes"""
    migration_files = []
    for line in changes_text.split('\n'):
        if 'Migrations/' in line and line.strip().endswith('.php'):
            parts = line.strip().split()
            if parts:
                file_path = parts[0] if not parts[0].startswith(('M', 'A', 'D')) else parts[1] if len(parts) > 1 else None
                if file_path and 'Migrations/' in file_path:
                    migration_files.append(file_path)
    return migration_files

def ask_claude_with_cache(client, prompt, use_cache=True):
    """Make a Claude API call with optional caching
    
    Args:
        client: Anthropic client instance
        prompt: The prompt text or list of content blocks
        use_cache: Whether to use prompt caching (default True)
    
    Returns:
        Parsed JSON response
    """
    # Build message content with cache control if enabled
    if use_cache and isinstance(prompt, str):
        content = [
            {
                "type": "text",
                "text": prompt,
                "cache_control": {"type": "ephemeral"}
            }
        ]
    elif isinstance(prompt, list):
        content = prompt
    else:
        content = prompt
    
    response = client.messages.create(
        model="claude-opus-4-20250514",
        max_tokens=8000,
        messages=[
            {"role": "user", "content": content}
        ]
    )
    
    response_text = response.content[0].text
    
    # Remove markdown code blocks if present
    if response_text.startswith('```'):
        response_text = response_text.split('```')[1]
        if response_text.startswith('json'):
            response_text = response_text[4:]
        response_text = response_text.strip()
    
    return json.loads(response_text)

def ask_claude_overview():
    """Step 1: Analyze overview and business context (fast, cacheable)"""
    client = Anthropic(api_key=ANTHROPIC_API_KEY)
    
    prompt = f"""You are a technical documentation specialist analyzing a merged pull request for a CodeIgniter 4 pizzaria API project.

# Project Context (CACHEABLE)
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

# Your Task
Provide a high-level overview analysis in JSON format:

{{
  "overview": {{
    "summary": "Brief 2-3 sentence summary of what was implemented in Brazilian Portuguese",
    "business_context": "What business problem does this solve? (Brazilian Portuguese)",
    "status": "Completed"
  }},
  "technical_details": {{
    "changes_made": [
      "Bullet point 1 of technical changes made (Brazilian Portuguese)",
      "Bullet point 2 of technical changes made (Brazilian Portuguese)"
    ]
  }}
}}

INSTRUCTIONS:
- Focus on WHAT WAS DONE (past tense), not what will be done
- Use Brazilian Portuguese for descriptions
- Be concise but informative
- Return ONLY valid JSON, no markdown formatting"""

    print("📋 Step 1/4: Analisando overview e contexto de negócio...")
    result = ask_claude_with_cache(client, prompt, use_cache=True)
    print("   ✅ Overview completo")
    return result

def ask_claude_endpoints(controller_files):
    """Step 2: Analyze endpoints from controller files (parallel possible)"""
    if not controller_files:
        print("📡 Step 2/4: Nenhum controller modificado, pulando análise de endpoints...")
        return []
    
    client = Anthropic(api_key=ANTHROPIC_API_KEY)
    all_endpoints = []
    
    print(f"📡 Step 2/4: Analisando {len(controller_files)} controller(s)...")
    
    for i, controller_file in enumerate(controller_files, 1):
        # Get relevant diff for this file
        file_diff = ""
        if controller_file in full_diff:
            # Extract diff section for this file (simplified)
            start_idx = full_diff.find(controller_file)
            if start_idx != -1:
                end_idx = full_diff.find("diff --git", start_idx + 1)
                file_diff = full_diff[start_idx:end_idx if end_idx != -1 else start_idx + 5000]
        
        prompt = f"""Analyze this controller file from a CodeIgniter 4 API project.

File: {controller_file}

Code Changes:
{file_diff[:3000]}

Return JSON array of endpoints found in this file:

[
  {{
    "method": "POST|GET|PUT|DELETE",
    "path": "/api/exact/path",
    "description": "What this endpoint does (Brazilian Portuguese)",
    "file_location": "{controller_file}",
    "request_params": {{
      "param1": "type - description in Portuguese"
    }},
    "request_headers": {{
      "Authorization": "Bearer <token>",
      "Content-Type": "application/json"
    }},
    "request_example": {{
      "param1": "example_value"
    }},
    "response_success": {{
      "data": {{}},
      "message": "Success"
    }},
    "response_error": {{
      "error": "Error message",
      "code": "ERROR_CODE"
    }},
    "business_rules": [
      "Business rule 1 in Portuguese"
    ],
    "validations": [
      "Validation 1 in Portuguese"
    ]
  }}
]

IMPORTANT:
- Only include endpoints ACTUALLY in this file
- Extract real parameter names from code
- Use Brazilian Portuguese for descriptions
- Return empty array [] if no endpoints found
- Return ONLY valid JSON"""

        try:
            endpoints = ask_claude_with_cache(client, prompt, use_cache=False)
            if endpoints:
                all_endpoints.extend(endpoints)
                print(f"   ✅ [{i}/{len(controller_files)}] {controller_file}: {len(endpoints)} endpoint(s)")
            else:
                print(f"   ⚠️  [{i}/{len(controller_files)}] {controller_file}: nenhum endpoint encontrado")
        except Exception as e:
            print(f"   ❌ [{i}/{len(controller_files)}] Erro ao analisar {controller_file}: {e}")
    
    return all_endpoints

def ask_claude_database(migration_files):
    """Step 3: Analyze database changes from migrations"""
    if not migration_files:
        print("🗄️  Step 3/4: Nenhuma migration encontrada, pulando análise de DB...")
        return {"database_changes": [], "integration_points": []}
    
    client = Anthropic(api_key=ANTHROPIC_API_KEY)
    
    migrations_context = "\n".join([f"- {mf}" for mf in migration_files])
    
    prompt = f"""Analyze database migrations from a CodeIgniter 4 project.

Migration Files:
{migrations_context}

Code Context (partial):
{full_diff[:5000]}

Return JSON with database analysis:

{{
  "database_changes": [
    "Description of table/column change 1 (Brazilian Portuguese)",
    "Description of table/column change 2 (Brazilian Portuguese)"
  ],
  "integration_points": [
    "External integration or system connection if any (Brazilian Portuguese)"
  ]
}}

INSTRUCTIONS:
- Focus on DDL changes (tables, columns, indexes)
- Mention DML changes if important (seeds, data updates)
- Use Brazilian Portuguese
- Return empty arrays if nothing found
- Return ONLY valid JSON"""

    print(f"🗄️  Step 3/4: Analisando {len(migration_files)} migration(s)...")
    result = ask_claude_with_cache(client, prompt, use_cache=False)
    print("   ✅ Análise de banco completa")
    return result

def ask_claude_deployment():
    """Step 4: Analyze deployment requirements"""
    client = Anthropic(api_key=ANTHROPIC_API_KEY)
    
    prompt = f"""Analyze deployment requirements from these changes.

Files Changed:
{changes}

Commits:
{commits}

Return JSON with deployment notes:

{{
  "deployment_notes": {{
    "environment_variables": [
      "ENV_VAR - description (if any new vars needed)"
    ],
    "configuration_changes": [
      "Config change description (if any)"
    ],
    "migration_steps": [
      "Step 1 to deploy",
      "Step 2 to deploy"
    ],
    "dependencies": [
      "New composer package (if any)"
    ]
  }}
}}

INSTRUCTIONS:
- Only include if actually needed
- Use Brazilian Portuguese for descriptions
- Return empty arrays if nothing needed
- Return ONLY valid JSON"""

    print("🚀 Step 4/4: Analisando requisitos de deployment...")
    result = ask_claude_with_cache(client, prompt, use_cache=False)
    print("   ✅ Análise de deployment completa")
    return result

def ask_claude_for_analysis(additional_files=[]):
    """Orchestrate chunked analysis with caching
    
    This breaks down the analysis into 4 focused steps:
    1. Overview + Business Context (cached, fast)
    2. Endpoints Analysis (per controller file)
    3. Database Changes (if migrations exist)
    4. Deployment Notes
    
    Total time: 4-7 minutes (well under 10min limit)
    """
    
    print("🤖 Iniciando análise chunked com Claude AI...")
    print("=" * 60)
    
    # Extract relevant files for targeted analysis
    controller_files = extract_controller_files(changes)
    migration_files = extract_migration_files(changes)
    
    print(f"📊 Arquivos identificados:")
    print(f"   - Controllers: {len(controller_files)}")
    print(f"   - Migrations: {len(migration_files)}")
    print()
    
    # Step 1: Overview (1-2 min)
    overview_result = ask_claude_overview()
    
    # Step 2: Endpoints (1-3 min depending on number of files)
    endpoints = ask_claude_endpoints(controller_files)
    
    # Step 3: Database (1-2 min if migrations exist)
    db_result = ask_claude_database(migration_files)
    
    # Step 4: Deployment (1 min)
    deployment_result = ask_claude_deployment()
    
    # Merge all results
    final_analysis = {
        "overview": overview_result.get("overview", {}),
        "technical_details": {
            "changes_made": overview_result.get("technical_details", {}).get("changes_made", []),
            "database_changes": db_result.get("database_changes", []),
            "integration_points": db_result.get("integration_points", [])
        },
        "endpoints": endpoints,
        "deployment_notes": deployment_result.get("deployment_notes", {})
    }
    
    print()
    print("=" * 60)
    print("✅ Análise chunked completa!")
    print(f"   📋 Overview: OK")
    print(f"   📡 Endpoints: {len(endpoints)}")
    print(f"   🗄️  DB Changes: {len(final_analysis['technical_details']['database_changes'])}")
    print(f"   🚀 Deployment: OK")
    
    return final_analysis

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
    
    if deployment.get('migration_steps'):
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
    if not any([deployment.get('environment_variables'), deployment.get('configuration_changes'), 
                deployment.get('migration_steps'), deployment.get('dependencies')]):
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
    
    # Opcionalmente, adicionar arquivos extras
    # Exemplo: diagramas, screenshots, arquivos de configuração específicos
    additional_files = []
    
    # Buscar arquivos de diagrama na PR (se existirem)
    diagram_extensions = ['.png', '.jpg', '.jpeg', '.pdf']
    if os.path.exists('pr-files'):
        for file in os.listdir('pr-files'):
            if any(file.endswith(ext) for ext in diagram_extensions):
                additional_files.append(os.path.join('pr-files', file))
    
    try:
        analysis = ask_claude_for_analysis(additional_files)
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
