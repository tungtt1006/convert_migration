# Convert migration
Convert file txt from dbdiagram to migration file. Made in Viet Nam with love.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg?style=flat-square)](https://php.net/)

# Usage
- Install package:

```
composer require tung/export-migration
```
- Create file `export-migration.txt` in the top-most directory of your project.
- Copy text format from dbdiagram and paste into `export-migration.txt`:

  ![image](https://user-images.githubusercontent.com/88578610/172752159-6f1aae2b-6fb7-4ddc-8cfa-c6b95be82d63.png)

- Finally, running `./vendor/bin/phptung` and files are created in `database/migrations`.

# Set up Docker for dev
- Build image:

```
docker build -f ./docker/Dockerfile -t convert_migration .
```
- Run container:
```
docker run -v $(pwd):/var/www/html -p 8000:8000 --name convert convert_migration
```