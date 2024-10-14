# Development Roadmap

1. Migration
  - Create migration and seeder
2. API
  - Create API methods
  - Filtering
  - Pagination
3. UI
  - Create Vue Components


## Challenges:

- JSON column searching in MySQL does not have as much support as there is in PostgreSQL, which caused some of our search methods to have issues. JSON columns can save you time and offer you a bunch of flexibility, however in a production environment I would use a separate table for indgredients and steps. It may add some complexity to our search logic, to have to include related tables however we could possibly see some performance enhancements by using indexed columns for the related tables.