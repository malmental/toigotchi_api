## Filosofia:

```bash
Auth = identidad
Pets = dominio
AI = comportamiento
```
Así se puede evolucionar todo sin romper nada.

Base URL:
/api/v1

# AUTH ENDPOINTS (Publico)

## Register:
POST /api/v1/auth/register

Body:
{
  "name": "User Name",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Response:
{
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com"
    },
    "token": "access_token_here"
  }
}

## Login:
POST /api/v1/auth/login

Body:
{
  "email": "user@example.com",
  "password": "password123"
}

Reponse:
{
  "data": {
    "token": "access_token_here",
    "token_type": "Bearer"
  }
}

## Refresh token:
POST /api/v1/auth/refresh

## Forgot password:
POST /api/v1/auth/forgot-password

Body:
{
  "email": "user@example.com"
}

## Reset password:
POST /api/v1/auth/reset-password

## Email verificarion:
GET /api/v1/auth/email/verify/{id}/{hash}

## Resend verification:
POST /api/v1/auth/email/resend

Protegidos:
Todos con auth:api

## Curent user:
GET /api/v1/auth/me

Response:
{
  "data": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com"
  }
}

## Logout actual token:
POST /api/v1/auth/logout

## Logout all devices:
POST /api/v1/auth/logout-all

## List active sessions/tokens
GET /api/v1/auth/sessions

Response:
{
  "data": [
    {
      "id": 1,
      "device": "Chrome on MacOS",
      "last_used_at": "2026-04-29"
    }
  ]
}

## Delete session/token:
DELETE /api/v1/auth/sessions/{tokenId}

PROFILE / ACCOUNT
Separados de Auth ya que es ≠ de Profile

## Get profile:
GET /api/v1/profile

## Update profile:
PATCH /api/v1/profile

Body:
{
  "name": "User Updated"
}

## Change password:
PATCH /api/v1/profile/password

## Delete account:
DELETE /api/v1/profile

Estructura REST para usar:
routes/
└── api.php

Ejemplo limpio:
```bash
Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::post('/register', RegisterController::class);
        Route::post('/login', LoginController::class);

        Route::middleware('auth:api')->group(function () {
            Route::get('/me', MeController::class);
            Route::post('/logout', LogoutController::class);
            Route::post('/logout-all', LogoutAllController::class);
            Route::get('/sessions', SessionIndexController::class);
            Route::delete('/sessions/{token}', SessionDestroyController::class);
        });
    });
});
```

## Endpoint de acciones
POST /api/v1/pets/{pet}/actions
GET  /api/v1/pets/{pet}/actions  (historial)

Controller:
- PetActionController (muy thin - solo valida, autoriza, despacha, responde)
Actions (domain logic):
- app/Actions/Pets/FeedPetAction.php
- app/Actions/Pets/PlayWithPetAction.php
- app/Actions/Pets/SleepPetAction.php
- app/Actions/Pets/CleanPetAction.php
- app/Actions/Pets/HealPetAction.php
- app/Actions/Pets/TalkPetAction.php
Dispatcher:
- PetActionManager (usa match para rutear al action correcto)
Enum:
- PetActionType enum (PHP 8.1+)
Modelo:
- PetAction (log de acciones en BD)
Migration:
- pet_actions table (pet_id, type, payload, effects_applied, created_at)

## Endpoint AI Chat
POST /api/v1/pets/{pet}/chat
GET  /api/v1/pets/{pet}/memories  → lista de recuerdos

Body: { "message": "how are you feeling?" }
Response: { "reply": "I'm hungry and tired..." }

---

# TODOS LOS ENDPOINTS IMPLEMENTADOS (v1)

Base URL:
/api

Headers requeridos en todos los endpoints protegidos:
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json

---

## AUTH (sin protección)

### Login
POST /api/login

Body (JSON):
{
  "email": "user1@telsur.cl",
  "password": "password123"
}

Response (JSON):
{
  "access_token": "base64_token_here",
  "token_type": "Bearer"
}

### Register
POST /api/register

Body (JSON):
{
  "name": "Tu Nombre",
  "email": "tu@email.com",
  "password": "password123"
}

Response (JSON):
{
  "access_token": "base64_token_here",
  "token_type": "Bearer"
}

---

## PROTEGIDOS

### Ver usuario actual
GET /api/me

Response (JSON):
{
  "id": 1,
  "name": "Salem",
  "email": "salem@telsur.cl"
}

### Logout
POST /api/logout

Response (JSON):
{
  "message": "Logged out successfully"
}

---

## PETS CRUD

### Listar pets
GET /api/v1/pets

Response (JSON):
{
  "data": [
    {
      "id": 1,
      "name": "Mochi",
      "species": "blobcat",
      "health": 100,
      "energy": 85,
      "hunger": 15,
      "cleanliness": 90,
      "mood": "happy",
      "is_alive": true,
      "created_at": "2026-04-29T18:00:00Z"
    }
  ]
}

### Crear pet
POST /api/v1/pets

Body (JSON):
{
  "name": "Mochi",
  "species": "blobcat"
}

Species válidos: blobcat, foxkid, draggle

Response (JSON): 201 Created
{
  "data": {
    "id": 1,
    "name": "Mochi",
    ...
  }
}

### Ver pet
GET /api/v1/pets/{id}

Response (JSON):
{
  "data": {
    "id": 1,
    "name": "Mochi",
    ...
  }
}

### Actualizar pet
PATCH /api/v1/pets/{id}

Body (JSON):
{
  "name": "Pepito"
}

Response (JSON):
{
  "data": {
    "id": 1,
    "name": "Pepito",
    ...
  }
}

### Eliminar pet
DELETE /api/v1/pets/{id}

Response: 204 No Content

---

## ACTIONS

### Ejecutar acción
POST /api/v1/pets/{id}/actions

Body (JSON) - FEED:
{
  "type": "feed",
  "payload": {
    "food": "apple"
  }
}

Body (JSON) - PLAY:
{
  "type": "play"
}

Body (JSON) - SLEEP:
{
  "type": "sleep"
}

Body (JSON) - CLEAN:
{
  "type": "clean"
}

Body (JSON) - HEAL:
{
  "type": "heal"
}

Body (JSON) - TALK:
{
  "type": "talk",
  "payload": {
    "message": "Hello!"
  }
}

Response (JSON):
{
  "message": "Action executed successfully",
  "effects": {
    "hunger": -20,
    "mood": 5,
    "energy": 0,
    "health": 0,
    "cleanliness": 0
  },
  "pet": {
    "id": 1,
    "name": "Mochi",
    "mood": "happy"
  }
}

### Historial de acciones
GET /api/v1/pets/{id}/actions

Response (JSON):
{
  "data": [
    {
      "id": 1,
      "pet_id": 1,
      "type": "feed",
      "payload": {"food": "apple"},
      "effects_applied": {"hunger": -20, "mood": 5},
      "created_at": "2026-04-29T18:00:00Z"
    }
  ]
}

---

## ACTION QUOTA

### Ver quota actual
GET /api/v1/pets/{id}/quota

Response (JSON):
{
  "used": 2,
  "limit": 3,
  "remaining": 1,
  "resets_at": "2026-05-11T13:00:00Z",
  "is_exhausted": false,
  "window_start": "2026-05-11T12:00:00Z"
}

Cada pet tiene 3 acciones por hora. Cuando se agota, acciones devuelven 429.

---

## DECAY LOGS

### Ver historial de decay
GET /api/v1/pets/{id}/decay-logs

Response (JSON):
{
  "data": [
    {
      "id": 1,
      "pet_id": 1,
      "hours_elapsed": 3,
      "changes": {
        "hunger": 45,
        "energy": -30,
        "cleanliness": -24,
        "health": 0
      },
      "created_at": "2026-05-11T12:00:00Z"
    }
  ]
}

Devuelve hasta 20 entradas de las últimas 24h, ordenadas por más reciente.

---

## AI CHAT

### Chat con IA
POST /api/v1/pets/{id}/chat

Body (JSON):
{
  "message": "How are you feeling today?"
}

Response (JSON):
{
  "reply": "I'm feeling happy! Thanks for asking.",
  "pet": {
    "id": 1,
    "name": "Mochi",
    "mood": "happy"
  }
}

### Ver memorias
GET /api/v1/pets/{id}/memories

Response (JSON):
{
  "data": [
    {
      "id": 1,
      "content": "How are you feeling today?",
      "ai_response": "I'm feeling happy! Thanks for asking.",
      "created_at": "2026-04-29T18:00:00Z"
    }
  ]
}

---

## SCHEDULER (artisan commands)

### Aplicar decay por minuto (existente)
php artisan pets:decay

Se ejecuta automáticamente cada minuto via Schedule::command('pets:decay')->everyMinute();

### Aplicar decay por hora (nuevo)
php artisan pets:hourly-decay

Runs hourly. Calculates time elapsed since last decay, applies cumulative decay (max 24h cap), y guarda logs en pet_decay_logs.

---

## STATS DEL PET

Todos los stats van de 0 a 100:
- health: 100 = healthy, 0 = dead
- energy: 100 = full energy, 0 = exhausted
- hunger: 0 = full, 100 = starving
- cleanliness: 100 = clean, 0 = dirty

Mood values: happy, neutral, tired, dirty, angry

is_alive: true/false

---

## TABLAS DE LA BASE DE DATOS

users
pets
pet_actions
pet_memories
pet_quotas
pet_decay_logs
oauth_clients (Passport)
oauth_access_tokens (Passport)

---

## POSTMAN COLLECTION

Crear collection "Toigotchi API"
Agregar environment "Toigotchi Local":
  baseUrl: http://localhost:8000
  token: {del login response}

Requests:
1. Login (POST /api/login)
2. List Pets (GET /api/v1/pets)
3. Create Pet (POST /api/v1/pets)
4. Get Pet (GET /api/v1/pets/{id})
5. Update Pet (PATCH /api/v1/pets/{id})
6. Delete Pet (DELETE /api/v1/pets/{id})
7. Feed Pet (POST /api/v1/pets/{id}/actions)
8. Play Pet (POST /api/v1/pets/{id}/actions)
9. Sleep Pet (POST /api/v1/pets/{id}/actions)
10. Clean Pet (POST /api/v1/pets/{id}/actions)
11. Heal Pet (POST /api/v1/pets/{id}/actions)
12. Talk Pet (POST /api/v1/pets/{id}/actions)
13. Action History (GET /api/v1/pets/{id}/actions)
14. Chat with AI (POST /api/v1/pets/{id}/chat)
15. Pet Memories (GET /api/v1/pets/{id}/memories)




Notas:
Core Application:
- app/Http/Controllers/Api/AuthController.php - Login/register/logout with Passport accessToken
- app/Http/Controllers/Api/PetController.php - CRUD for pets
- app/Http/Controllers/Api/V1/PetActionController.php - Execute actions and view history
- app/Http/Controllers/Api/V1/PetChatController.php - AI chat and memories
- app/Http/Controllers/Api/V1/PetQuotaController.php - Action quota management
- app/Http/Controllers/Api/V1/PetDecayLogController.php - Decay history logs
- app/Models/Pet.php - Pet model with relationships
- app/Models/User.php - User model with OAuthenticatable
- app/Models/PetAction.php - Action log model
- app/Models/PetMemory.php - AI conversation memory model
- app/Models/PetQuota.php - Action quota model
- app/Models/PetDecayLog.php - Decay history log model
- app/Services/OllamaService.php - Ollama AI client
- app/Services/PetActionManager.php - Action dispatcher with match

Domain Layer:
- app/Domain/Pet/Services/PetDecayService.php - Stat decay logic
- app/Domain/Pet/Services/PetMoodService.php - Mood calculation
- app/Domain/Pet/Services/PetStateService.php - State enums aggregation
- app/Domain/Pet/Services/PetStatBoundaryService.php - Clamping 0-100
- app/Domain/Pet/Services/SpeciesModifierService.php - Species-specific modifiers
- app/Domain/Pet/Services/PetPromptBuilder.php - AI prompt construction
- app/Domain/Pet/ValueObjects/PetStats.php - Stats value object
- app/Domain/Pet/Events/PetDied.php - Death event
- app/Domain/Pet/Events/PetStatChanged.php - Stat change event
- app/Domain/Pet/Events/PetDecayedWhileAway.php - Hourly decay event
- app/Domain/Pet/Listeners/LogPetDecay.php - Listener for decay logging

Console Commands:
- app/Console/Commands/HourlyDecayCommand.php - Hourly decay scheduler command
- app/Console/Commands/UpdatePetStatsCommand.php - Minute-based decay command

Actions:
- app/Actions/Pets/FeedPetAction.php
- app/Actions/Pets/PlayWithPetAction.php
- app/Actions/Pets/SleepPetAction.php
- app/Actions/Pets/CleanPetAction.php
- app/Actions/Pets/HealPetAction.php
- app/Actions/Pets/TalkPetAction.php
- app/Actions/Pets/PetActionContract.php - Interface

Enums:
- app/Enums/PetActionType.php
- app/Enums/HungerState.php
- app/Enums/EnergyState.php
- app/Enums/CleanlinessState.php
- app/Enums/HealthState.php

Configuration:
- config/auth.php - Auth guards with api => ['driver' => 'passport']
- routes/api.php - All API routes with auth:api middleware
- bootstrap/providers.php - Includes Passport's AuthServiceProvider

Seeders:
- database/seeders/UserSeeder.php - user1@telsur.cl, user2@telsur.cl / password123
- database/seeders/PetSeeder.php - Mochi, Ember, Scales, Ghost (user1) + Luna, Drake (user2)
- database/seeders/PetActionSeeder.php
- database/seeders/PetMemorySeeder.php

Tests:
- tests/Feature/V1/PetApiTest.php - 8 CRUD tests
- tests/Feature/V1/PetActionTest.php - 7 action tests
- tests/Feature/V1/PetChatTest.php - 6 chat tests
- tests/Feature/Domain/Pet/ - Domain service tests (PetDecayServiceTest, PetMoodServiceTest, etc.)

Documentation:
- README.md - Comprehensive project documentation
- Endpoints.md - All endpoints documented for Postman
- public/docs/ - Scribe generated documentation (http://127.0.0.1:8000/docs)

Rutas actuales:
POST   /api/register                   [public]
POST   /api/login                      [public]
POST   /api/logout                     [auth]
GET    /api/me                         [auth]
---
GET    /api/v1/pets                    [auth]
POST   /api/v1/pets                    [auth]
GET    /api/v1/pets/{pet}              [auth]
PUT    /api/v1/pets/{pet}.             [auth]
DELETE /api/v1/pets/{pet}              [auth]
POST   /api/v1/pets/{pet}/actions      [auth]
GET    /api/v1/pets/{pet}/actions      [auth]
GET    /api/v1/pets/{pet}/quota        [auth]
POST   /api/v1/pets/{pet}/chat         [auth]
GET    /api/v1/pets/{pet}/memories     [auth]
GET    /api/v1/pets/{pet}/decay-logs   [auth]