# 🚀 Laravel REST API — Autenticación con Sanctum (v1 & v2)

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Sanctum](https://img.shields.io/badge/Laravel_Sanctum-Auth-orange?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

API RESTful construida con **Laravel 12**, que implementa autenticación mediante **Laravel Sanctum** y está versionada en dos capas:

- **v1** — Respuestas estructuradas con `PostResource` propio (campos directos, sin metadata extra)
- **v2** — Respuestas enriquecidas con `PostResource` + `PostCollection` (metadata personalizada con `organizacion` y `autor`)

El login es compartido entre ambas versiones a través de un único `LoginController`.  
El middleware `auth:sanctum` se aplica directamente sobre cada `apiResource` en las rutas.

---

## 📁 Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── LoginController.php        ← Compartido por v1 y v2
│   │       ├── V1/
│   │       │   └── PostController.php     ← CRUD posts v1
│   │       └── V2/
│   │           └── PostController.php     ← CRUD posts v2
│   └── Resources/
│       ├── V1/
│       │   └── PostResource.php           ← Resource individual v1
│       └── V2/
│           ├── PostResource.php           ← Resource individual v2
│           └── PostCollection.php         ← Colección paginada con meta custom
├── Models/
│   ├── User.php
│   └── Post.php
routes/
└── api.php
```

---

## ⚙️ Requisitos

- PHP >= 8.2
- Composer
- Laravel 12.x
- MySQL / PostgreSQL / SQLite
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

---

## 🛠️ Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/magovenegas/Api
cd Api

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_db
DB_USERNAME=root
DB_PASSWORD=

# 5. Ejecutar migraciones
php artisan migrate

# 6. (Opcional) Seeders
php artisan db:seed

# 7. Levantar servidor
php artisan serve
```

> Sanctum viene preinstalado en Laravel 12. Si necesitas publicar su configuración:
> ```bash
> php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
> ```

---

## 🔐 Autenticación — Laravel Sanctum

Esta API usa **tokens Bearer** generados por Sanctum.  
El `LoginController` es compartido y funciona para ambas versiones.

### Flujo de autenticación

```
Cliente  →  POST /api/login (form-data: usuario, email, password)
                    ↓
            LoginController valida credenciales
                    ↓
            Retorna token Bearer
                    ↓
Cliente  →  GET /api/v1/posts  →  Authorization: Bearer {token}
Cliente  →  GET /api/v2/posts  →  Authorization: Bearer {token}
```

Para acceder a rutas protegidas, incluir en el header:

```
Authorization: Bearer {token}
```

---

## 📌 Endpoints

### 🔑 Login — `/api/login`

> Compartido entre v1 y v2. Acepta `multipart/form-data`.

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| `POST` | `/api/login` | Iniciar sesión y obtener token Bearer | ❌ |

**Request — `form-data`:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `name` | string | Nombre del token (usado como identificador) |
| `email` | string | Correo del usuario |
| `password` | string | Contraseña |

**Response `200`:**
```json
{
    "token": "2|uxX7KMlJeQ0Wrn1TTAbvIurLM4hH0wkwBnDDFsGS01815fa1",
    "message": "Success"
}
```

**Response `401` — Credenciales inválidas:**
```json
{
    "message": "Unauthenticated"
}
```

---

### 🔵 Versión 1 — `/api/v1/posts`

> Usa `PostResource` de V1. Devuelve los campos `title`, `categoria` y `contenido`.  
> Middleware `auth:sanctum` aplicado directamente sobre el `apiResource`.  
> Métodos disponibles: `index`, `show`, `destroy`.

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/v1/posts` | Listar todos los posts paginados | ✅ |
| `GET` | `/api/v1/posts/{id}` | Ver un post por ID | ✅ |
| `DELETE` | `/api/v1/posts/{id}` | Eliminar un post | ✅ |

#### `GET /api/v1/posts`

**Response:**
```json
{
    "data": [
        {
            "title": "ELIMINANDO ENEMIGOS",
            "categoria": "VALIDO",
            "contenido": "PRUEBA"
        },
        {
            "title": "AVENTUAS PHP",
            "categoria": "PRUEBA",
            "contenido": "php 8.1"
        },
        {
            "title": "Laravel Prueba",
            "categoria": "estudio",
            "contenido": "programacion"
        }
    ],
    "links": {
        "first": "http://api.test/api/v1/posts?page=1",
        "last": "http://api.test/api/v1/posts?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://api.test/api/v1/posts",
        "per_page": 15,
        "to": 7,
        "total": 7
    }
}
```

#### `GET /api/v1/posts/{id}`

**Response:**
```json
{
    "data": {
        "title": "Laravel Prueba",
        "categoria": "estudio",
        "contenido": "programacion"
    }
}
```

---

### 🟣 Versión 2 — `/api/v2/posts`

> Usa `PostResource` y `PostCollection` de V2.  
> Devuelve `post_name`, `categoria` y `created_at` con metadata personalizada (`organizacion`, `autor`).  
> Middleware `auth:sanctum` aplicado directamente sobre el `apiResource`.  
> Métodos disponibles: `index`, `show`, `destroy`.

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/v2/posts` | Listar posts paginados con metadata | ✅ |
| `GET` | `/api/v2/posts/{id}` | Ver un post por ID | ✅ |
| `DELETE` | `/api/v2/posts/{id}` | Eliminar un post | ✅ |

#### `GET /api/v2/posts` — con `PostCollection`

**Response:**
```json
{
    "data": [
        {
            "post_name": "ELIMINANDO ENEMIGOS",
            "categoria": "VALIDO",
            "created_at": "2026-03-03 16:00"
        },
        {
            "post_name": "AVENTUAS PHP",
            "categoria": "PRUEBA",
            "created_at": "2026-03-03 14:19"
        },
        {
            "post_name": "Laravel Prueba",
            "categoria": "estudio",
            "created_at": "2026-01-20 12:00"
        }
    ],
    "meta": {
        "organizacion": "Empresa",
        "autor": "Camilo Venegas",
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://api.test/api/v2/posts",
        "per_page": 15,
        "to": 7,
        "total": 7
    },
    "links": {
        "first": "http://api.test/api/v2/posts?page=1",
        "last": "http://api.test/api/v2/posts?page=1",
        "prev": null,
        "next": null
    }
}
```

#### `GET /api/v2/posts/{id}` — con `PostResource` V2

**Response:**
```json
{
    "data": {
        "post_name": "Laravel Prueba",
        "categoria": "estudio",
        "created_at": "2026-01-20 12:00"
    }
}
```

---

## 🗺️ Definición de Rutas (`routes/api.php`)

```php
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\V1\PostController as PostV1;
use App\Http\Controllers\Api\V2\PostController as PostV2;

// ── Login compartido (sin middleware) ────────────────────────
Route::post('login', [LoginController::class, 'login']);

// ── V1 ───────────────────────────────────────────────────────
Route::apiResource('v1/posts', PostV1::class)
    ->only(['index', 'show', 'destroy'])
    ->middleware('auth:sanctum');

// ── V2 ───────────────────────────────────────────────────────
Route::apiResource('v2/posts', PostV2::class)
    ->only(['index', 'show', 'destroy'])
    ->middleware('auth:sanctum');
```

> Se exponen solo los métodos `index`, `show` y `destroy` mediante `.only([...])`.

---

## 🧩 API Resources & Collections

### `V1/PostResource.php`

Campos directos del modelo para v1: `title`, `categoria`, `contenido`.

```php
// app/Http/Resources/V1/PostResource.php

public function toArray(Request $request): array
{
    return [
        'title'     => $this->title,
        'categoria' => $this->categoria,
        'contenido' => $this->content,
    ];
}
```

---

### `V2/PostResource.php`

Campos enriquecidos para v2: renombra `title` como `post_name` y agrega `created_at` formateado.

```php
// app/Http/Resources/V2/PostResource.php

public function toArray(Request $request): array
{
    return [
        'post_name' => $this->title,
        'categoria' => $this->categoria,
        'created_at' => $this->created_at->format('Y-m-d H:i'),
    ];
}
```

---

### `V2/PostCollection.php`

Colección paginada con metadata personalizada (`organizacion`, `autor`) fusionada con la paginación de Laravel.

```php
// app/Http/Resources/V2/PostCollection.php

public function toArray(Request $request): array
{
    return [
        'data' => $this->collection,
        'meta' => [
            'organizacion' => 'Empresa',
            'autor'        => 'Camilo Venegas',
        ],
    ];
}
```

> En el `PostController` de v2:
> ```php
> // index → colección paginada con PostCollection
> return new PostCollection(Post::paginate(15));
>
> // show → recurso individual con PostResource de V2
> return new \App\Http\Resources\V2\PostResource($post);
> ```

---

## 🔑 LoginController

Recibe `form-data` con `name`, `email` y `password`. Usa `name` como nombre del token de Sanctum.

```php
// app/Http/Controllers/Api/LoginController.php

public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
        'name'     => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'token'   => $request->user()->createToken($request->name)->plainTextToken,
            'message' => 'Success',
        ], 200);
    }

    return response()->json([
        'message' => 'Unauthenticated',
    ], 401);
}
```

> El campo `name` del form-data se usa como identificador del token en Sanctum  
> (visible en la tabla `personal_access_tokens`).

---

## 🛡️ Middleware

El middleware se aplica **directamente en cada `apiResource`**, sin grupos globales.

| Ruta | Middleware | Acceso |
|------|------------|--------|
| `POST /api/login` | ❌ ninguno | Público |
| `GET /api/v1/posts` | `auth:sanctum` | Token requerido |
| `GET /api/v1/posts/{id}` | `auth:sanctum` | Token requerido |
| `DELETE /api/v1/posts/{id}` | `auth:sanctum` | Token requerido |
| `GET /api/v2/posts` | `auth:sanctum` | Token requerido |
| `GET /api/v2/posts/{id}` | `auth:sanctum` | Token requerido |
| `DELETE /api/v2/posts/{id}` | `auth:sanctum` | Token requerido |

---

## 🔄 Diferencias entre v1 y v2

| Característica | v1 | v2 |
|----------------|----|----|
| Resource propio | ✅ `V1/PostResource` | ✅ `V2/PostResource` |
| Collection propia | ❌ | ✅ `V2/PostCollection` |
| Campos devueltos | `title`, `categoria`, `contenido` | `post_name`, `categoria`, `created_at` |
| Metadata custom (`organizacion`, `autor`) | ❌ | ✅ |
| Paginación con `links` y `meta` | ✅ (automático Laravel) | ✅ (enriquecido) |
| Métodos expuestos | `index`, `show`, `destroy` | `index`, `show`, `destroy` |
| Login compartido | ✅ | ✅ |

---

## 🧪 Testing con cURL

```bash
# ── Login ────────────────────────────────────────────────────
curl -X POST http://api.test/api/login \
  -F "name=mi-token" \
  -F "email=john@example.com" \
  -F "password=password123"

# ── V1: Listar posts ─────────────────────────────────────────
curl -X GET http://api.test/api/v1/posts \
  -H "Authorization: Bearer {tu_token}"

# ── V1: Ver post por ID ──────────────────────────────────────
curl -X GET http://api.test/api/v1/posts/1 \
  -H "Authorization: Bearer {tu_token}"

# ── V1: Eliminar post ────────────────────────────────────────
curl -X DELETE http://api.test/api/v1/posts/1 \
  -H "Authorization: Bearer {tu_token}"

# ── V2: Listar posts con metadata custom ─────────────────────
curl -X GET http://api.test/api/v2/posts \
  -H "Authorization: Bearer {tu_token}"

# ── V2: Ver post por ID ──────────────────────────────────────
curl -X GET http://api.test/api/v2/posts/1 \
  -H "Authorization: Bearer {tu_token}"
```

---

## 📄 Licencia

Este proyecto está bajo la licencia [MIT](LICENSE).

---

> Desarrollado con ❤️ usando **Laravel 12** + **Sanctum** + **API Resources**
