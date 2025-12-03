# Architecture Overview

## Request Lifecycle

1. **Entry Point**: All requests are directed to `public/index.php`.
2. **Application Initialization**: `Src\Core\Application` is instantiated.
3. **Container**: A dependency injection container is created.
4. **Router**: The router is initialized with the container.
5. **Dispatch**: The application dispatches the request method and URI to the router.
6. **Middleware**: Global and route-specific middleware are executed.
7. **Controller/Handler**: The matched route handler is executed.
8. **Response**: The response is sent back to the client.

## Service Container

SwiftPHP uses a simple but powerful service container (`Src\Core\Container`) for dependency injection.
It supports:
- Automatic resolution of classes.
- Singleton binding.
- Interface binding.

## Router

The `Src\Core\Router` handles URL matching and dispatching.
It supports:
- Standard HTTP methods (GET, POST, PUT, DELETE).
- Dynamic parameters (e.g., `/users/{id}`).
- Middleware pipelines.
