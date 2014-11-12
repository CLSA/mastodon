<?php
/**
 * error_codes.inc.php
 * 
 * This file is where all error codes are defined.
 * All error code are named after the class and function they occur in.
 */

/**
 * Error number category defines.
 */
define( 'ARGUMENT_MASTODON_BASE_ERRNO',   140000 );
define( 'DATABASE_MASTODON_BASE_ERRNO',   240000 );
define( 'LDAP_MASTODON_BASE_ERRNO',       340000 );
define( 'NOTICE_MASTODON_BASE_ERRNO',     440000 );
define( 'PERMISSION_MASTODON_BASE_ERRNO', 540000 );
define( 'RUNTIME_MASTODON_BASE_ERRNO',    640000 );
define( 'SYSTEM_MASTODON_BASE_ERRNO',     740000 );
define( 'TEMPLATE_MASTODON_BASE_ERRNO',   840000 );

/**
 * "argument" error codes
 */
define( 'ARGUMENT__MASTODON_UI_PUSH_PROXY_FORM_NEW____CONSTRUCT__ERRNO',
        ARGUMENT_MASTODON_BASE_ERRNO + 1 );
define( 'ARGUMENT__MASTODON_UI_WIDGET_IMPORT_ADD__SETUP__ERRNO',
        ARGUMENT_MASTODON_BASE_ERRNO + 2 );

/**
 * "database" error codes
 * 
 * Since database errors already have codes this list is likely to stay empty.
 */

/**
 * "ldap" error codes
 * 
 * Since ldap errors already have codes this list is likely to stay empty.
 */

/**
 * "notice" error codes
 */
define( 'NOTICE__MASTODON_DATABASE_CONTACT_FORM__IMPORT__ERRNO',
        NOTICE_MASTODON_BASE_ERRNO + 1 );
define( 'NOTICE__MASTODON_DATABASE_IMPORT_ENTRY__IMPORT__ERRNO',
        NOTICE_MASTODON_BASE_ERRNO + 2 );
define( 'NOTICE__MASTODON_UI_PULL_SERVICE_PARTICIPANT_RELEASE__VALIDATE__ERRNO',
        NOTICE_MASTODON_BASE_ERRNO + 3 );
define( 'NOTICE__MASTODON_UI_PUSH_BASE_FORM_ENTRY_NEW____CONSTRUCT__ERRNO',
        NOTICE_MASTODON_BASE_ERRNO + 4 );
define( 'NOTICE__MASTODON_UI_PUSH_IMPORT_NEW__VALIDATE__ERRNO',
        NOTICE_MASTODON_BASE_ERRNO + 5 );
define( 'NOTICE__MASTODON_UI_PUSH_IMPORT_NEW__EXECUTE__ERRNO',
        NOTICE_MASTODON_BASE_ERRNO + 6 );
define( 'NOTICE__MASTODON_UI_PUSH_SERVICE_PARTICIPANT_RELEASE__VALIDATE__ERRNO',
        NOTICE_MASTODON_BASE_ERRNO + 7 );

/**
 * "permission" error codes
 */

/**
 * "runtime" error codes
 */
define( 'RUNTIME__MASTODON_BUSINESS_SESSION__PROCESS_REQUESTED_SITE_AND_ROLE__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 1 );
define( 'RUNTIME__MASTODON_DATABASE_BASE_FORM__WRITE_FORM__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 2 );
define( 'RUNTIME__MASTODON_DATABASE_CONSENT_FORM__IMPORT__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 3 );
define( 'RUNTIME__MASTODON_DATABASE_CONTACT_FORM__IMPORT__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 4 );
define( 'RUNTIME__MASTODON_DATABASE_PROXY_FORM__IMPORT__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 5 );
define( 'RUNTIME__MASTODON_UI_PULL_BASE_FORM_DOWNLOAD__EXECUTE__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 6 );
define( 'RUNTIME__MASTODON_UI_PUSH_ALTERNATE_NEW__FINISH__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 7 );
define( 'RUNTIME__MASTODON_UI_PUSH_BASE_FORM_ADJUDICATE__EXECUTE__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 8 );
define( 'RUNTIME__MASTODON_UI_PUSH_BASE_FORM_ENTRY_EDIT__VALIDATE__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 9 );
define( 'RUNTIME__MASTODON_UI_PUSH_CONSENT_NEW__EXECUTE__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 10 );
define( 'RUNTIME__MASTODON_UI_PUSH_PROXY_FORM_NEW__FINISH__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 11 );

/**
 * "system" error codes
 * 
 * Since system errors already have codes this list is likely to stay empty.
 * Note the following PHP error codes:
 *      1: error,
 *      2: warning,
 *      4: parse,
 *      8: notice,
 *     16: core error,
 *     32: core warning,
 *     64: compile error,
 *    128: compile warning,
 *    256: user error,
 *    512: user warning,
 *   1024: user notice
 */

/**
 * "template" error codes
 * 
 * Since template errors already have codes this list is likely to stay empty.
 */

