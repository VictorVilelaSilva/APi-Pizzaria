# Copilot Instructions - API Bankop

## Project Overview
CodeIgniter 4 pizzaria API with modular architecture. Sistema pizzaria (pizzeria operations platform) with multi-environment deployment and migration-based development workflow.

## Architecture Patterns

### Modular Structure
- **Modules directory**: `app/Modules/` contains ~40+ feature modules (AdminConta, Authentication, Bancarizacao, EWorker, Payment, etc.)
- Each module has: `Controllers/`, `Models/`, `Services/`, `DTO/`, `Routes.php`
- Routes auto-discovered via `app/Config/Routes.php` iterator over module directories
- Use `Modules\` namespace prefix (configured in composer.json PSR-4 autoload)

### Error Handling Convention
Note defined wet 

### Data Flow Pattern (Controller → Service → Model)
```php
// Controller validates & delegates to Service
class ManagerController extends ResourceController {
    public function createUser() {
        $service = new ManagerService();
        return $service->createUser($this->request->getJSON(true));
    }
}

// Service contains business logic, validation, transactions
class ManagerService {
    public function createUser(array $userData) {
        $profileDTO = CreateProfileDTO::get($userData);
        ManagerValidation::execute('CREATE_PROFILE', $profileDTO, 'EMANAGER023');
        // Business logic with database transactions
    }
}
```

### DTO Pattern
- All DTOs extend `app/DTO/DTO.php` with `set()` abstract method
- Use `static::get(array $row)` for single record transformation
- Use `static::getAll(array $rows)` for collections
- DTOs clean/format data: `removeNonNumeric()`, `strtoupper()`, etc.
- Example: `app/Modules/AdminConta/DTO/Manager/CreateProfileDTO.php`

### Entity Pattern
- Entities extend `App\Modules\HBEntity` for rich domain objects with business logic
- Use `$attributes` array to define entity properties (snake_case database fields)
- Use `$datamap` array to map snake_case fields to camelCase property names
- Implement custom setters for validation and data transformation:
```php
public function setDocumentId($documentId) {
    if($documentId && !validar_cpfcnpj(removeNonNumeric($documentId))){
        throw new ErrorTrait("EBGRNTEEC0004");
    }
    $this->attributes['documentId'] = removeNonNumeric($documentId);
}
```
- Use `$casts` array for automatic type conversion (`'date'`, `'value'`, etc.)
- Entities can contain nested entities (e.g., `AddressEntity`, `PhoneEntity`)
- Validation happens at setter level - throw ErrorTrait for invalid data
- Example: `app/Modules/Person/Entities/PersonEntity.php`

### Model Convention (HBIModel)
- Extend `App\Models\HBIModel` (not CodeIgniter\Model directly)
- HBIModel provides: filtering (`listByFilters()`), pagination, soft deletes, logging
- Use `DBGroup` property for multiple databases: `'default'`, `'accountDigital'`, `'message'`
- Models auto-inject `id_user_ins`, `uuid`, log changes via callbacks
- Example: `app/Modules/AdminConta/Models/CobanADModel.php`

### Validation Pattern
```php
// Custom validation extending HBIValidation
ManagerValidation::execute('CREATE_PROFILE', $data, 'EMANAGER023');
// Type, data, error code if fails
```

## Database Management

### Multi-Database Setup
Three database groups in `app/Config/Database.php`:
- `default` - Main bankop database
- `message` - Message/notification database  
- `accountDigital` - Digital account system
- `oauth` - OAuth authentication

### Migration Workflow (Critical)
Migrations organized by PDB (Project/Branch) ticket:
```bash
# Create migration for branch PDB4193
php spark make:migration PDB4193/bankop/ddl/migration_name  # Schema changes
php spark make:migration PDB4193/bankop/dml/seed_data       # Data changes
php spark make:migration PDB4193/message/ddl/table_change   # Message DB

# Run specific branch migrations
php spark migrate:run:branch -n PDB4193
php spark migrate:run:branch -n PDB4193 -g message  # Specific group

# Rollback specific branch
php spark migrate:rollback:branch -n PDB4193
```
- Structure: `app/Database/Migrations/PDB{ticket}/{database}/{ddl|dml}/`
- DDL = schema changes, DML = data/seed changes
- Custom commands in `app/Commands/` for namespace-based migrations

### Docker Local Development
```bash
docker-compose -f docker-compose-database.yml up -d
# Change .env: database.default.hostname = localhost
```

## Testing

### Test Structure
- Uses PestPHP (`vendor/bin/pest`) configured in composer.json
- Test organization: `tests/{unit,integration,e2e}/`
- Run: `composer test` or `composer test:coverage`
- Database tests: Configure in `phpunit.xml` (copy from phpunit.xml.dist)

## Security & Authentication

### Filter System
- `isAdmin` filter checks admin permissions with route parameter: `['filter' => 'isAdmin:/digital/view']`
- `auth` filter for general authentication
- Filters in `app/Filters/`, configured in `app/Config/Filters.php`
- Example: `app/Filters/IsAdminFilter.php`

### Session Management
- Google Authenticator MFA support via `app/GoogleAuthenticator/`
- Session data accessed via `session('id_user')`, `session('id_person')`
- MFA tables: `reg_user_google_authenticator`

## Key Helper Functions
Located in `app/Helpers/`:
- `removeNonNumeric($valor)` - Strip non-numeric chars
- `validar_cpfcnpj($cpfCnpj)` - Validate Brazilian tax IDs
- `generate_uuid()` - Generate UUIDs for records
- `camelToSnakeCase($string)` - Convert naming conventions
- `removeMasksFilter($str)` - Clean input filters

## Development & Debugging

### Debug Functions (app/Helpers/debug_helper.php)
**IMPORTANT**: These are development-only utilities, NOT error handling functions:
- `badRequest($variable)` - **Debug tool to inspect variable contents during development**
  - Outputs variable as JSON with 400 status and stops execution
  - Used for quick debugging: `badRequest($userData);` to see what's inside
  - **Do NOT confuse with error handling** - this is for debugging only
  - Remove before committing production code
  
### When Debugging
```php
// Inspect variable contents during development
$result = $model->find($id);
badRequest($result); // See what's in $result

// Check query results
$users = $model->listUsers($filters);
badRequest($users); // Inspect the array structure
```

**Note**: For production error handling, always use `throw new ErrorTrait('ERRORCODE')` instead.

## RabbitMQ Integration
- Consumer classes: `app/RabbitMq/Consumers/`
- Publisher: `app/RabbitMq/PublishUseCase.php`
- Config: `app/Config/RabbitMQ.php`
- Command: `app/Commands/RabbitConsumerBackOffice.php`

## Environment Management
Seven environments with specific URLs and databases (see README.md):
- development (dev branch) → bancarizador-dev.api-hbi.com.br
- test (teste branch) → bancarizador-teste.api-hbi.com.br
- production (main branch) → bancarizador.api-hbi.com.br
- Configure via `.env` file (copy from `env` file)

## Common Tasks

### Adding New Module Feature
1. Create Service class extending business logic
2. Create DTO classes for request/response transformation
3. Add Validation class extending `HBIValidation`
4. Create Controller extending `ResourceController`
5. Define routes in module's `Routes.php`
6. Apply appropriate filters for authentication/authorization
7. Create migrations in PDB{ticket} structure

### Transaction Pattern
```php
$db = db_connect();
$db->transStart();
try {
    // Multiple operations
    $model->insert($data);
    $otherModel->update($id, $updates);
    $db->transComplete();
    if ($db->transStatus() === false) {
        throw new ErrorTrait('ERRORCODE');
    }
} catch (\Exception $e) {
    $db->transRollback();
    throw $e;
}
```

## Code Style
- Use snakecase for databases with DBGroup `default` and `message` column names (legacy convention): `created_at`, `id_user_ins`
- Use uppercase for databases with DBGroup `accountDigital` column names (legacy convention): `CPFCNPJ`, `DATAHORAINSERT`

- camelCase for PHP variables and methods
- PascalCase for class names
- Namespace structure follows directory structure
- Always use type hints where possible (PHP 7.4+)
