Requirements
============

1. Participant Database
-----------------------
  1.1. schema
    * CLSA id (UID)
    * first name
    * last name
    * hin
    * status (permanent condition)
    * standard/comprehensive
    * date of birth
    * gender
    * other demographics ???
    * note listing
  1.2. operations
    * activate/deactivate
    * add
    * edit
    * list
    * view
    * archive (delete)
2. Contact Database
-------------------
  2.1 addresses
    2.1.1 participant addresses
      2.1.1.1. schema
        * up to two address lines
        * city
        * province/state
        * country (Canada or US only)
        * postal/zip code
        * rank
        * availability (months)
        * note
      2.1.1.2. operations
        * activate/deactivate
        * add
        * edit
        * list
        * view
        * delete
    2.1.2 alternate addresses
      2.1.2.1. schema
        * association
        * first name
        * last name
        * up to two address lines
        * city
        * province/state
        * country (Canada or US only)
        * postal/zip code
        * note
      2.1.2.2. operations
        * activate/deactivate
        * add
        * edit
        * list
        * view
        * delete
  2.2 phone numbers
    2.2.1 participant phone numbers
      2.2.1.1. schema
        * type/origin (home, work, mobile, etc)
        * number
        * associated address
        * rank
        * note
      2.2.1.2. operations
        * activate/deactivate
        * add
        * edit
        * list
        * view
        * delete
    2.2.2 alternate addresses
      2.2.2.1. schema
        * type/origin (home, work, mobile, etc)
        * number
        * associated address
        * note
      2.2.2.2. operations
        * activate/deactivate
        * add
        * edit
        * list
        * view
        * delete
3. Note listings
----------------
  3.1. scope
    Note listings will be available for every participant.
  3.2. schema
    * date/time
    * user
    * sticky
    * text
  3.3. operations
    * add
    * edit
    * delete
    * conversation list
4. Auditing
-----------
  4.1. scope
    Auditing will be performed on all participants, addresses and phone numbers.
  4.2. operations
    * automatic auditing (edit, delete)
    * undo
    * redo
    * history
5. Reporting
------------
  5.1. scope
    Reports can be performed on all participants, addresses and phone numbers.
  5.2. operations
    5.2.1. restricted listing based on:
      * full or partial match: first name
      * full or partial match: last name
      * status (permanent condition)
      * standard/comprehensive
      * full or partial match, before or after: date of birth
      * gender
      * other demographics ???
    5.2.2. other reports ???

Assumptions/Questions
---------------------
* The PRM is not responsible for tracking participant's activities in other systems.
  For instance, determining the last time a participant was called for an interview is the
  reponsibility of the CATI.
