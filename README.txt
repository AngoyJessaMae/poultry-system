POULTRY MANAGEMENT SYSTEM
SYSTEM SPECIFICATION AND DATABASE SCHEMA
======================================================================

1. SYSTEM OVERVIEW
----------------------------------------------------------------------

The Poultry Management System is a Laravel web application for managing
farm users, stations, chicken batches, feeding, growth, health,
mortality, sales, daily production, and management reports.

The system is designed for authenticated farm personnel. It maintains
the current number of live birds in each batch and records the
operational activities that affect a batch over time.

Primary objectives:

* Centralize poultry farm records in one system.
* Track the location and status of every chicken batch.
* Record feeding, growth, health, mortality, and sales activities.
* Support daily production monitoring and production standards.
* Provide management reports and PDF/Excel export support.
* Restrict features according to user role and account status.
* Preserve activity and health history for accountability.


2. TECHNOLOGY STACK
----------------------------------------------------------------------

Backend framework       Laravel 12
Programming language    PHP 8.2 or newer
Database                SQLite by default; MySQL-compatible migrations
Frontend                Blade templates, Tailwind CSS, Alpine.js
Asset bundler           Vite
Authentication          Laravel Breeze authentication
PDF reports             barryvdh/laravel-dompdf
Excel reports           maatwebsite/excel
Testing                 PHPUnit and Laravel feature tests

The database structure is defined by the files in:
database/migrations/


3. USER ROLES AND ACCESS
----------------------------------------------------------------------

The application currently uses the following operational roles:

MANAGER
* Accesses the manager dashboard.
* Manages users and account activation.
* Views reports and reporting dashboards.
* Can access manager-level operational records according to policy.

WORKER
* Accesses the worker dashboard.
* Records stations, batches, feeding, growth, health, mortality, and
  sales activities according to policy.
* Views operational records assigned to or created by the worker where
  applicable.

All users also have:

* A name, email address, password, and optional contact number.
* An active/inactive account state.
* Policy acceptance tracking.
* House-rules acknowledgement tracking.
* Email verification support.


4. FUNCTIONAL REQUIREMENTS
----------------------------------------------------------------------

4.1 Authentication and account management

The system shall:

* Allow users to authenticate with an email address and password.
* Support email verification and password reset.
* Enforce role-based access through authenticated middleware and policies.
* Allow managers to activate or manage worker accounts.
* Track whether required policy documents and house rules were viewed
  and accepted or acknowledged.
* Allow users to maintain their profile information.

4.2 Station management

The system shall:

* Store a station name and age range in days.
* Store the maximum station capacity.
* Store an optional station description.
* Associate batches with their current station.

4.3 Batch management

The system shall:

* Create a uniquely identified batch using a batch code.
* Record the farm name, arrival date, initial quantity, and current
  quantity.
* Record the initial bird weight in grams.
* Store the selected feeding method.
* Track the batch status as active, sold, or archived.
* Record the user who created the batch and an optional flockman.
* Mark a batch as below expected production and store a reason.
* Track station transfers in the batch movement history.

4.4 Feeding management

The system shall:

* Record the batch and user associated with a feeding log.
* Record feed type, quantity, feeding method, and feeding time.
* Support the configured Free Choice and Restricted feeding methods.
* Support batch feeding schedule notifications and dismissal records.

4.5 Growth monitoring

The system shall:

* Record the batch, user, date, age, and average weight.
* Classify growth as chick, grower, or market ready.
* Flag an individual growth record when it is below the expected value.
* Store optional notes for the growth observation.

4.6 Health monitoring

The system shall:

* Record observations, medication information, dosage, and notes for a
  batch.
* Record affected, dead, and recovered counts.
* Track health status, remarks, and remedy information.
* Optionally associate a health record with a mortality record.
* Preserve health record history, including status and outcome counts.

4.7 Mortality management

The system shall:

* Record mortality by batch, station, date, and user.
* Store the number of deceased birds.
* Store an optional suspected cause and notes.
* Update operational counts according to the application's mortality
  workflow.

4.8 Sales management

The system shall:

* Record a sale date, batch, user, heads sold, total weight, price per
  kilogram, total amount, and optional buyer name.
* Deduct the sold heads from the batch current quantity when a sale is
  recorded.
* Allow sales to be listed, viewed, and newly recorded.
* NOT provide edit or delete functionality for sales. Sales are
  intentionally treated as immutable operational transactions.

4.9 Production monitoring and reporting

The system shall:

* Store production standards by bird age.
* Store daily production values for a batch and date.
* Prevent duplicate daily production records for the same batch and date.
* Generate reports for feeding, growth, health, medication, mortality,
  sales, and profit.
* Support report date ranges.
* Support PDF and Excel report exports where implemented.
* Record the user who generated a report.

4.10 Audit and notifications

The system shall:

* Record user activities, descriptions, IP addresses, user agents, and
  login/logout information in the activity log.
* Record dismissed batch feeding notifications by user, batch, and
  schedule time.


5. NON-FUNCTIONAL REQUIREMENTS
----------------------------------------------------------------------

Security
* Passwords shall be stored using Laravel's password hashing support.
* Protected pages shall require authentication and the required policy
  and house-rules acknowledgements.
* Authorization shall be enforced server-side through middleware and
  policies, not only through hidden interface controls.
* Foreign-key constraints shall preserve referential integrity.

Data integrity
* Batch codes shall be unique.
* Quantities and counts shall use unsigned numeric fields where
  negative values are invalid.
* Production records shall be unique per batch and recorded date.
* Notification dismissals shall be unique per user, batch, and schedule.
* Sales shall not expose update or delete operations.

Maintainability
* Database changes shall be delivered through Laravel migrations.
* Domain entities shall be represented by Eloquent models.
* Validation shall be performed by form request classes where provided.
* Automated tests shall cover authorization and application behavior.

Scalability
* The relational structure supports multiple users, stations, batches,
  and activity records.
* Foreign-key indexes and unique constraints support reliable lookups.
* Report generation is separated from transactional data entry.


6. DATABASE RELATIONSHIP SUMMARY
----------------------------------------------------------------------

users 1 --- many batches (created_by)
users 1 --- many batches (flockman_id, optional)
users 1 --- many feeding_logs
users 1 --- many growth_records
users 1 --- many health_records
users 1 --- many mortality_records
users 1 --- many sales
users 1 --- many reports
users 1 --- many activity_logs
users 1 --- many batch_movements (optional user)
users 1 --- many notification_dismissals
users 1 --- many daily_production_records

stations 1 --- many batches
stations 1 --- many mortality_records
stations 1 --- many batch_movements (from station)
stations 1 --- many batch_movements (to station)

batches 1 --- many feeding_logs
batches 1 --- many growth_records
batches 1 --- many health_records
batches 1 --- many mortality_records
batches 1 --- many sales
batches 1 --- many batch_movements
batches 1 --- many notification_dismissals
batches 1 --- many daily_production_records

health_records 1 --- many health_record_histories
mortality_records 1 --- many health_records (optional reference)


7. APPLICATION DATABASE TABLES
----------------------------------------------------------------------

The following definitions describe the final logical columns after all
current migrations have been applied. "PK" means primary key and "FK"
means foreign key.


7.1 USERS TABLE

Purpose: Stores manager and worker accounts and account acceptance
metadata.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
name                          VARCHAR(255)          Required
email                         VARCHAR(255)          Unique, required
contact_number                VARCHAR(30)           Nullable
email_verified_at             TIMESTAMP             Nullable
password                      VARCHAR(255)          Required, hashed
remember_token                VARCHAR(100)          Nullable
role                          VARCHAR(255)          Default: worker
is_active                     BOOLEAN               Default: true
policy_revision               VARCHAR(255)          Nullable
policy_viewed_at              TIMESTAMP             Nullable
policy_accepted_at            TIMESTAMP             Nullable
house_rules_revision          VARCHAR(255)          Nullable
house_rules_viewed_at         TIMESTAMP             Nullable
house_rules_acknowledged_at   TIMESTAMP             Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.2 STATIONS TABLE

Purpose: Defines farm housing stations and their age ranges/capacity.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
name                          VARCHAR(255)          Required
capacity                      INTEGER               Default: 0
min_age_days                  INTEGER UNSIGNED     Required
max_age_days                  INTEGER UNSIGNED     Required
description                   TEXT                  Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.3 BATCHES TABLE

Purpose: Stores each flock/batch and its current inventory state.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_code                    VARCHAR(255)          Unique, required
farm_name                     VARCHAR(255)          Nullable
station_id                    BIGINT UNSIGNED       FK -> stations.id
created_by                    BIGINT UNSIGNED       FK -> users.id
flockman_id                   BIGINT UNSIGNED       FK -> users.id, nullable
arrival_date                  DATE                  Required
initial_quantity              INTEGER UNSIGNED     Required
current_quantity              INTEGER UNSIGNED     Required
initial_weight_grams          DECIMAL(8,2)          Default: 45.00
feeding_method                ENUM                  twice_daily,
                                                        four_times_daily,
                                                        unlimited
status                        ENUM                  active, sold, archived
is_below_expected             BOOLEAN               Default: false
below_expected_reason         TEXT                  Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.4 FEEDING_LOGS TABLE

Purpose: Records feed administration for a batch.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_id                      BIGINT UNSIGNED       FK -> batches.id,
                                                        cascade on delete
user_id                       BIGINT UNSIGNED       FK -> users.id,
                                                        cascade on delete
feed_type                     VARCHAR(50)           Required
quantity                      DECIMAL(6,2)          Required
method                        ENUM                  Free Choice, Restricted
feeding_time                  DATETIME              Required
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.5 GROWTH_RECORDS TABLE

Purpose: Records weight and growth-stage observations.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_id                      BIGINT UNSIGNED       FK -> batches.id
user_id                       BIGINT UNSIGNED       FK -> users.id
recorded_date                 DATE                  Required
age_days                      INTEGER UNSIGNED     Required
average_weight_grams          DECIMAL(8,2)          Required
growth_stage                  ENUM                  chick, grower,
                                                        market_ready
is_below_expected             BOOLEAN               Default: false
notes                         TEXT                  Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.6 HEALTH_RECORDS TABLE

Purpose: Records health observations, treatment, and outcomes for a
batch.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_id                      BIGINT UNSIGNED       FK -> batches.id
user_id                       BIGINT UNSIGNED       FK -> users.id
affected_count                INTEGER UNSIGNED     Default: 1
dead_count                    INTEGER UNSIGNED     Default: 0
observation                   TEXT                  Required
medication_name               VARCHAR(255)          Nullable
dosage_amount                VARCHAR(255)          Nullable
dosage_unit                   VARCHAR(255)          Nullable
notes                         TEXT                  Nullable
status                        VARCHAR(255)          Default:
                                                        under_treatment
remarks                       TEXT                  Nullable
remedy                        TEXT                  Nullable
mortality_record_id           BIGINT UNSIGNED       FK -> mortality_records.id,
                                                        nullable, null on delete
recorded_date                 DATETIME              Required
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.7 HEALTH_RECORD_HISTORIES TABLE

Purpose: Preserves outcome/history snapshots for health records.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
health_record_id              BIGINT UNSIGNED       FK -> health_records.id,
                                                        cascade on delete
user_id                       BIGINT UNSIGNED       FK -> users.id
affected_count                INTEGER UNSIGNED     Required
dead_count                    INTEGER UNSIGNED     Required
recovered_count               INTEGER UNSIGNED     Required
status                        VARCHAR(255)          Required
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.8 MORTALITY_RECORDS TABLE

Purpose: Records bird mortality events by batch and station.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_id                      BIGINT UNSIGNED       FK -> batches.id
station_id                    BIGINT UNSIGNED       FK -> stations.id
user_id                       BIGINT UNSIGNED       FK -> users.id
mortality_date                DATE                  Required
count                         INTEGER UNSIGNED     Required
suspected_cause               VARCHAR(255)          Nullable
notes                         TEXT                  Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.9 SALES TABLE

Purpose: Stores completed sales transactions.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_id                      BIGINT UNSIGNED       FK -> batches.id
user_id                       BIGINT UNSIGNED       FK -> users.id
sale_date                     DATE                  Required
heads_sold                    INTEGER UNSIGNED     Required
total_weight_kg               DECIMAL(8,2)          Required
price_per_kg                  DECIMAL(8,2)          Required
total_amount                  DECIMAL(10,2)         Required
buyer_name                    VARCHAR(255)          Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable

Sales are append-only in the current application. The sales resource
provides index, create, store, and show operations only. Edit, update,
and delete operations are intentionally unavailable.


7.10 BATCH_MOVEMENTS TABLE

Purpose: Records movement of a batch between stations.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_id                      BIGINT UNSIGNED       FK -> batches.id,
                                                        cascade on delete
from_station_id               BIGINT UNSIGNED       FK -> stations.id,
                                                        nullable, set null on delete
to_station_id                 BIGINT UNSIGNED       FK -> stations.id,
                                                        nullable, set null on delete
user_id                       BIGINT UNSIGNED       FK -> users.id,
                                                        nullable, set null on delete
moved_at                      TIMESTAMP             Required
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.11 PRODUCTION_STANDARDS TABLE

Purpose: Stores expected production values by bird age.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
age_days                      INTEGER UNSIGNED     Unique, required
mortality_percent             DECIMAL(8,4)          Nullable
live_weight_grams             DECIMAL(10,2)         Nullable
feed_grams                    DECIMAL(12,2)         Nullable
fcr                           DECIMAL(10,6)         Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.12 DAILY_PRODUCTION_RECORDS TABLE

Purpose: Stores daily production measurements for each batch.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
batch_id                      BIGINT UNSIGNED       FK -> batches.id,
                                                        cascade on delete
recorded_by                   BIGINT UNSIGNED       FK -> users.id,
                                                        restrict on delete
recorded_date                 DATE                  Required
mortality_heads               INTEGER UNSIGNED     Default: 0
actual_live_weight_grams      DECIMAL(10,2)         Nullable
feed_type                     VARCHAR(50)           Nullable
actual_feed_grams             DECIMAL(12,2)         Nullable
notes                         TEXT                  Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable

Unique constraint: batch_id + recorded_date.


7.13 REPORTS TABLE

Purpose: Stores report-generation metadata.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
report_id                     BIGINT UNSIGNED       PK
generated_by                  BIGINT UNSIGNED       FK -> users.id,
                                                        cascade on delete
type                          ENUM                  feeding, growth, health,
                                                        medication, mortality,
                                                        sales, profit
period_start                  DATE                  Nullable
period_end                    DATE                  Nullable
generated_at                  DATETIME              Required


7.14 ACTIVITY_LOGS TABLE

Purpose: Stores security and operational audit events.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
user_id                       BIGINT UNSIGNED       FK -> users.id,
                                                        cascade on delete
activity_type                 VARCHAR(255)          Required
description                   TEXT                  Nullable
ip_address                    VARCHAR(255)          Nullable
user_agent                    TEXT                  Nullable
login_at                      TIMESTAMP             Nullable
logout_at                     TIMESTAMP             Nullable
session_id                    VARCHAR(255)          Nullable
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable


7.15 NOTIFICATION_DISMISSALS TABLE

Purpose: Stores dismissed feeding schedule notifications.

Field                         Type                  Key/Rules
----------------------------------------------------------------------
id                            BIGINT UNSIGNED       PK
user_id                       BIGINT UNSIGNED       FK -> users.id,
                                                        cascade on delete
batch_id                      BIGINT UNSIGNED       FK -> batches.id,
                                                        cascade on delete
schedule_time                 VARCHAR(255)          Required
created_at                    TIMESTAMP             Nullable
updated_at                    TIMESTAMP             Nullable

Unique constraint: user_id + batch_id + schedule_time.


8. LARAVEL SUPPORT TABLES
----------------------------------------------------------------------

These tables are created by the Laravel framework and support
authentication, sessions, caching, and queued jobs. They are not
business-domain tables.

password_reset_tokens
* email VARCHAR(255), primary key
* token VARCHAR(255)
* created_at TIMESTAMP, nullable

sessions
* id VARCHAR(255), primary key
* user_id BIGINT UNSIGNED, nullable and indexed
* ip_address VARCHAR(45), nullable
* user_agent TEXT, nullable
* payload LONGTEXT
* last_activity INTEGER, indexed

cache
* key VARCHAR(255), primary key
* value MEDIUMTEXT
* expiration INTEGER, indexed

cache_locks
* key VARCHAR(255), primary key
* owner VARCHAR(255)
* expiration INTEGER, indexed

jobs
* id BIGINT UNSIGNED, primary key
* queue VARCHAR(255), indexed
* payload LONGTEXT
* attempts UNSIGNED TINYINT
* reserved_at UNSIGNED INTEGER, nullable
* available_at UNSIGNED INTEGER
* created_at UNSIGNED INTEGER

job_batches
* id VARCHAR(255), primary key
* name VARCHAR(255)
* total_jobs INTEGER
* pending_jobs INTEGER
* failed_jobs INTEGER
* failed_job_ids LONGTEXT
* options MEDIUMTEXT, nullable
* cancelled_at INTEGER, nullable
* created_at INTEGER
* finished_at INTEGER, nullable

failed_jobs
* id BIGINT UNSIGNED, primary key
* uuid VARCHAR(255), unique
* connection TEXT
* queue TEXT
* payload LONGTEXT
* exception LONGTEXT
* failed_at TIMESTAMP, default current time


9. IMPORTANT BUSINESS RULES
----------------------------------------------------------------------

* A batch code must be unique.
* A batch belongs to one current station, while movement history may
  contain many station transfers.
* A batch's current quantity represents the current live inventory
  tracked by the application.
* A sale records the number of heads sold and decreases current
  quantity when successfully stored.
* Sales cannot be edited or deleted after recording.
* A mortality record belongs to both a batch and a station.
* A health record may reference a mortality record, but the reference
  is optional.
* A daily production record is unique for a batch on a given date.
* Production standards use one row per age in days.
* Deleting a batch cascades to related feeding logs, batch movements,
  notification dismissals, and daily production records where defined
  by the foreign-key rules.
* Deleting a user may cascade to selected records, restrict deletion,
  or set nullable references to null depending on the relationship.


10. DEVELOPMENT AND OPERATION
----------------------------------------------------------------------

Install PHP and Composer dependencies:

    composer install

Create the environment file and application key:

    copy .env.example .env
    php artisan key:generate

Run database migrations:

    php artisan migrate

Install frontend dependencies:

    npm install

Build frontend assets:

    npm run build

Start the Laravel development server:

    php artisan serve

Run the automated test suite:

    php artisan test

For local development, configure the database and mail settings in
.env. The repository includes database/database.sqlite for SQLite-based
development when configured by the application environment.


11. DOCUMENTATION SOURCE OF TRUTH
----------------------------------------------------------------------

This file summarizes the implemented system. For exact database
behavior, constraints, and migration order, consult:

* database/migrations/
* app/Models/
* app/Policies/
* app/Http/Controllers/
* app/Http/Requests/
* routes/web.php
* tests/

When a migration changes the schema, this document should be updated
to keep the data dictionary and system specification current.
