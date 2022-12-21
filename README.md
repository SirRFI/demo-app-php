This project aims to demonstrate selected basic concepts, such as but not limited to:

* RESTful API (CRUD with proper HTTP status codes)
    * `200 OK` when requesting collection of resources
    * `201 CREATED` when creating new resource
    * `200 OK` when getting or updating existing resource
    * `204 NO CONTENT` when deleting existing resource
    * `400 BAD REQUEST` when request data is not valid (not properly formatted, doesn't pass validation etc.)
    * `404 NOT FOUND` when requested resource doesn't exist
* Data mapping to objects
* Validation (limited to request for simplicity)
* Objects with enforced valid state via constructor
* Integration with external API
* Working with a database
* Testing
* Usage of [Symfony framework](https://symfony.com) and it's components (Serializer, Validator, HTTP Client, and more)

## Notes

* Some concepts are implemented despite being an overkill for the use case and introducing codebase inconsistency, just
  for sake of demonstrating them.
* Usually there's middle layer (business/domain, for example ProductService), that acts as a glue between input
  (ie: REST API) and output (ie: external API, database) layers. However, for this demo it wouldn't bring any value, so
  was skipped for simplicity.

### Product

* Products are integrated with [FakeStoreAPI](https://fakestoreapi.com), representing sample product in a shop.
* One controller ~~to rule them all~~ for all actions. Each action demonstrates step by step what is being done
  (decode/deserialize JSON into a class, validate the data and so on), easy to understand but repetitive flow.
* `AddProductCommand` or `UpdateProductCommand` (not to be mistaken with CLI commands) are meant to be immutable objects
  that are always in valid state, thus the extra logic in constructor. "Command" suffix means that it will be used to
  change state of the data, while "Query" would mean reading only.

### Task

* Tasks stored in database (using Doctrine ORM + MariaDB via Docker Compose), representing a task.
* Uses one controller per action (controller method called by router) using `__invoke()`. This approach can be useful
  when the all-in-one controller is too big for some reason (too many actions, API doc as PHP doc block, etc.), to
  better separate dependencies (assuming you don't want to pass them as action arguments), to avoid git conflicts and
  such.
* `ValueResolver`s allows asking for something as action's argument, while the logic behind obtaining them is
  abstracted. For example, `TaskDTO` is created from request body, using deserializer and validator
  (see `TaskDTOResolver`).
