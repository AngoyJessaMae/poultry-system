# Poultry System Database Schema

This document describes the database currently used by the application. It includes the operational tables, their important columns, foreign-key relationships, and an ERD.

The schema was checked against the active MySQL database on 2026-11-17. Run the Laravel migrations before relying on this document in another environment.

## Core business rules

- Station identifiers are fixed to `S1`, `S2`, and `S3`.
- Station feed types are `S1 = Starter`, `S2 = Booster`, and `S3 = Finisher`.
- A batch belongs to its current station through `batches.station_id`.
- Feeding logs and sales keep a station snapshot through their own `station_id`, so historical records remain attached to the station where the activity happened.
- Feed prices may be station-specific (`feed_prices.station_id`) or global (`NULL`). Station-specific prices take priority.
- `management_requests` stores approval history. Repeated approved or rejected requests are allowed; duplicate pending requests are prevented by application validation.

## Entity relationship diagram

The following ERD shows the application tables and their foreign-key relationships. Laravel infrastructure tables such as sessions, jobs, cache, and migrations are documented separately below because they are not part of the poultry workflow.

```mermaid
erDiagram
    USERS ||--o{ BATCHES : creates
    USERS ||--o{ BATCHES : flockman
    USERS ||--o{ BATCH_MOVEMENTS : records
    USERS ||--o{ FEEDING_LOGS : records
    USERS ||--o{ FEED_PRICES : creates
    USERS ||--o{ SALES : records
    USERS ||--o{ MANAGEMENT_REQUESTS : submits
    USERS ||--o{ MANAGEMENT_REQUESTS : reviews
    USERS ||--o{ MORTALITY_RECORDS : records
    USERS ||--o{ GROWTH_RECORDS : records
    USERS ||--o{ HEALTH_RECORDS : records
    USERS ||--o{ HEALTH_RECORD_HISTORIES : records
    USERS ||--o{ DAILY_PRODUCTION_RECORDS : records
    USERS ||--o{ QUANTITY_ADJUSTMENTS : approves
    USERS ||--o{ OPERATIONAL_EXPENSES : creates
    USERS ||--o{ OPERATIONAL_EXPENSES : cancels
    USERS ||--o{ OTHER_INCOMES : creates
    USERS ||--o{ OTHER_INCOMES : cancels
    USERS ||--o{ FINANCIAL_ADJUSTMENTS : creates
    USERS ||--o{ REPORTS : generates
    USERS ||--o{ ACTIVITY_LOGS : performs
    USERS ||--o{ NOTIFICATION_DISMISSALS : dismisses

    STATIONS ||--o{ BATCHES : contains
    STATIONS ||--o{ BATCH_MOVEMENTS : from
    STATIONS ||--o{ BATCH_MOVEMENTS : to
    STATIONS ||--o| STATION_FEEDING_CONFIGS : configures
    STATIONS ||--o{ FEEDING_LOGS : snapshot
    STATIONS ||--o{ FEED_PRICES : prices
    STATIONS ||--o{ SALES : snapshot
    STATIONS ||--o{ MORTALITY_RECORDS : snapshot
    STATIONS ||--o{ OPERATIONAL_EXPENSES : allocation

    BATCHES ||--o{ BATCH_MOVEMENTS : moves
    BATCHES ||--o{ FEEDING_LOGS : consumes
    BATCHES ||--o{ SALES : sold
    BATCHES ||--o{ GROWTH_RECORDS : measures
    BATCHES ||--o{ HEALTH_RECORDS : receives
    BATCHES ||--o{ MORTALITY_RECORDS : records
    BATCHES ||--o{ DAILY_PRODUCTION_RECORDS : reports
    BATCHES ||--o{ QUANTITY_ADJUSTMENTS : adjusts
    BATCHES ||--o{ OPERATIONAL_EXPENSES : allocation
    BATCHES ||--o{ NOTIFICATION_DISMISSALS : dismisses

    MORTALITY_RECORDS ||--o{ HEALTH_RECORDS : relates
    HEALTH_RECORDS ||--o{ HEALTH_RECORD_HISTORIES : history
```

## Application tables

`id` is an auto-incrementing primary key unless another key is shown. `created_at` and `updated_at` are present on the application tables unless noted otherwise.

### users

Authentication, authorization, and user policy state.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `name` | varchar, required |
| `email` | varchar, unique, required |
| `contact_number` | varchar, nullable |
| `email_verified_at` | timestamp, nullable |
| `password` | varchar |
| `remember_token` | varchar, nullable |
| `role` | varchar, default `worker` |
| `is_active` | boolean, default true |
| `policy_revision`, `house_rules_revision` | varchar, nullable |
| `policy_viewed_at`, `policy_accepted_at` | timestamp, nullable |
| `house_rules_viewed_at`, `house_rules_acknowledged_at` | timestamp, nullable |

### stations

Physical or operational poultry stations. Only `S1`, `S2`, and `S3` are supported identifiers.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `station_code` | varchar(50), unique, nullable for legacy rows |
| `name` | varchar, required |
| `capacity` | integer, required |
| `min_age_days`, `max_age_days` | unsigned integer |
| `description` | text, nullable |

### station_feeding_configs

Feeding schedule rules for each station. One row per station.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `station_id` | foreign key → `stations.id`, unique |
| `method` | varchar(30): `unlimited`, `twice_daily`, or `four_times_daily` |
| `schedule_times` | JSON, nullable |

### batches

Bird batches and their current station assignment.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `batch_code` | varchar, unique business identifier |
| `farm_name` | varchar, nullable |
| `station_id` | foreign key → `stations.id` |
| `created_by` | foreign key → `users.id` |
| `flockman_id` | nullable foreign key → `users.id` |
| `arrival_date` | date |
| `initial_quantity`, `current_quantity` | unsigned integer |
| `initial_weight_grams` | decimal(8,2), default 45.00 |
| `feeding_method` | enum: `twice_daily`, `four_times_daily`, `unlimited` |
| `status` | enum as stored in the current database |
| `is_below_expected` | boolean |
| `below_expected_reason` | text, nullable |

### batch_movements

Station transfer history for batches.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `batch_id` | foreign key → `batches.id` |
| `from_station_id` | nullable foreign key → `stations.id` |
| `to_station_id` | nullable foreign key → `stations.id` |
| `user_id` | nullable foreign key → `users.id` |
| `quantity` | unsigned integer, nullable |
| `moved_at` | timestamp |

### feeding_logs

Actual feed consumption records. `station_id` is intentionally stored as a historical snapshot.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `batch_id` | foreign key → `batches.id` |
| `station_id` | nullable foreign key → `stations.id` |
| `user_id` | foreign key → `users.id` |
| `feed_type` | varchar(50) |
| `quantity` | decimal(10,3), compatibility quantity |
| `quantity_kg` | decimal(10,3), nullable authoritative quantity |
| `unit` | varchar(10), default `kg` |
| `method` | enum: `Free Choice` or `Restricted` |
| `feeding_time` | datetime |
| `scheduled_at` | datetime, nullable |
| `notes` | text, nullable |
| constraint | unique `batch_id` + `scheduled_at` |

### feed_prices

Effective-dated feed prices. A `NULL` station is a global fallback price.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `feed_type` | varchar(100) |
| `station_id` | nullable foreign key → `stations.id` |
| `price_per_sack` | decimal(12,2) |
| `sack_weight_kg` | decimal(10,3), default 50.000 |
| `effective_from`, `effective_to` | date; end date nullable |
| `created_by` | foreign key → `users.id` |

### sales

Sales transactions for batches, including station snapshot and cancellation workflow.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `transaction_number` | nullable varchar, unique |
| `batch_id` | foreign key → `batches.id` |
| `station_id` | nullable foreign key → `stations.id` |
| `user_id` | foreign key → `users.id` |
| `sale_date` | date |
| `heads_sold` | unsigned integer |
| `total_weight_kg` | decimal(8,2) |
| `price_per_kg` | decimal(8,2) |
| `total_amount` | decimal(10,2) |
| `status` | varchar(20), default `completed` |
| `cancelled_at`, `cancelled_by`, `cancellation_reason` | nullable cancellation fields |
| `buyer_name` | varchar, nullable |

### management_requests

Worker-submitted station and batch requests awaiting manager approval.

| Column | Type / notes |
|---|---|
| `id` | bigint, primary key |
| `request_type` | varchar(30): station or batch |
| `request_key` | business identifier, such as station code or batch code |
| `requested_by` | foreign key → `users.id` |
| `status` | varchar(20), normally pending, approved, or rejected |
| `payload` | JSON |
| `reviewed_by` | nullable foreign key → `users.id` |
| `reviewed_at` | timestamp, nullable |
| `decision_notes` | text, nullable |
| indexes | status and request type/key/status indexes; historical duplicates allowed |

### production and health tables

| Table | Main columns and relationships |
|---|---|
| `daily_production_records` | `batch_id` → batches, `recorded_by` → users, `recorded_date`, mortality heads, live weight, feed type, actual feed grams, notes |
| `production_standards` | unique `age_days`, mortality percent, live weight grams, feed grams, FCR |
| `growth_records` | `batch_id` → batches, `user_id` → users, recorded date, age, average weight, growth stage, below-expected flag, notes |
| `mortality_records` | `batch_id` → batches, `station_id` → stations, `user_id` → users, mortality date, count, suspected cause, notes |
| `health_records` | `batch_id` → batches, `user_id` → users, affected/dead counts, status, observation, medication and dosage fields, optional `mortality_record_id` → mortality records, recorded date |
| `health_record_histories` | `health_record_id` → health records, `user_id` → users, affected/dead/recovered counts, status |

### financial tables

| Table | Main columns and relationships |
|---|---|
| `operational_expenses` | Description, category, amount, quantity/unit, expense date, allocation type, nullable station/batch links, status, cancellation fields, `created_by` → users |
| `other_incomes` | Description, category, amount, income date, status, cancellation fields, `created_by`/`cancelled_by` → users |
| `financial_adjustments` | Adjustment type, amount, reason, adjustment date, status, `created_by` → users |
| `reports` | `report_id` primary key, `generated_by` → users, report type, period dates, generated timestamp |

### audit and notification tables

| Table | Main columns and relationships |
|---|---|
| `activity_logs` | `user_id` → users, activity type, description, IP address, user agent, login/logout timestamps, session ID |
| `notifications` | Laravel notification record with UUID primary key, notifiable type/id, JSON/text data, read timestamp |
| `notification_dismissals` | `user_id` → users, `batch_id` → batches, schedule time |

### Laravel infrastructure tables

These tables support the framework and are not part of the poultry business ERD.

| Table | Purpose |
|---|---|
| `migrations` | Records applied Laravel migrations |
| `cache`, `cache_locks` | Application cache and cache locks |
| `jobs`, `job_batches`, `failed_jobs` | Queue jobs, batches, and failed queue records |
| `sessions` | Database-backed sessions |
| `password_reset_tokens` | Password reset tokens |

## Important historical-data behavior

- Moving a batch changes its current `batches.station_id`, but does not rewrite the `station_id` already stored on old feeding logs or sales. This preserves historical station reporting.
- Feed expense calculation uses the station recorded on each feeding log and the feed price effective on that feeding date.
- A price entered after a feeding date does not retroactively price that older feeding record unless an earlier effective date is entered.
- Existing batches were backfilled to match their station feeding configuration. Future station configuration changes also update the feeding method of batches currently assigned to that station.

## Source of truth

The migration files in [`database/migrations/`](database/migrations/) are the version-controlled source of truth for rebuilding the schema. This document is a readable reference and should be regenerated when a new migration changes tables, columns, indexes, or relationships.
