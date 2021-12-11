<?php

namespace Paladin\Enum;

use MyCLabs\Enum\Enum;

class TranslatorEnum extends Enum
{
    const INVALID_EMAIL_OR_PASSWORD = "invalid_email_or_password";
    const INVALID_EMAIL_FORMAT = "invalid_email_format";
    const INVALID_PASSWORD_FORMAT = "invalid_password_format";
    const STRING_MIN_LENGTH = "string_min_length";
    const STRING_MAX_LENGTH = "string_max_length";
}