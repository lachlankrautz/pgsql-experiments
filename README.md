# pgsql experiments

## Recursive Eloquent Relations

The user schema has a recursive relation through `mangaer_id`. The `User` class defines accessors for `dominion` and `chainOfCommand` using recursive queries. 

## Setup

You'll need to be running a postgres server and create the database `pgsql_experiments`.

```shell
cd pgsql-experiments

# Install dependencies
composer install

# Seed the database
./artisan migrate:fresh --seed
```

## Examples

Using these recursive relations it is easy to interact with the entire organisation.

```php
// See my reports
User::first()->reports->pluck('name');

// See my dominion
User::first()->dominion->pluck('name');

// Count the entire tree under the first user
User::first()->dominion->count();

// Count a subtree
User::first()->reports[2]->dominion->count();

// See the chain of command from the lowest point
User::last()->chainOfCommnad->pluck('name');

// Let someone go
User::last()->fire();

// Let a team go
User::first()->reports[2]->fireTeam();
```
