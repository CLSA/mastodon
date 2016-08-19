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

/**
 * "argument" error codes
 */
define( 'ARGUMENT__MASTODON_DATABASE_APPLICATION__RELEASE_PARTICIPANTS__ERRNO',
        ARGUMENT_MASTODON_BASE_ERRNO + 1 );
define( 'ARGUMENT__MASTODON_SERVICE_APPLICATION_PARTICIPANT_POST__EXECUTE__ERRNO',
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

/**
 * "permission" error codes
 */

/**
 * "runtime" error codes
 */
define( 'RUNTIME__MASTODON_DATABASE_BASE_FORM__IMPORT__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 1 );
define( 'RUNTIME__MASTODON_DATABASE_BASE_FORM__WRITE_FORM__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 2 );
define( 'RUNTIME__MASTODON_SERVICE_BASE_FORM_PATCH__EXECUTE__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 3 );
define( 'RUNTIME__MASTODON_SERVICE_PROXY_FORM_POST__EXECUTE__ERRNO',
        RUNTIME_MASTODON_BASE_ERRNO + 4 );

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

