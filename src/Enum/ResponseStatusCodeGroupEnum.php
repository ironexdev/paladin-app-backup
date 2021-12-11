<?php declare(strict_types=1);

namespace Paladin\Enum;

use MyCLabs\Enum\Enum;

class ResponseStatusCodeGroupEnum extends Enum
{
    const INFORMATIONAL = [
        ResponseStatusCodeEnum::CONTINUE,
        ResponseStatusCodeEnum::SWITCHING_PROTOCOLS,
        ResponseStatusCodeEnum::PROCESSING,
        ResponseStatusCodeEnum::EARLY_HINTS
    ];
    const SUCCESS = [
        ResponseStatusCodeEnum::OK,
        ResponseStatusCodeEnum::CREATED,
        ResponseStatusCodeEnum::ACCEPTED,
        ResponseStatusCodeEnum::NON_AUTHORITATIVE_INFORMATION,
        ResponseStatusCodeEnum::NO_CONTENT,
        ResponseStatusCodeEnum::RESET_CONTENT,
        ResponseStatusCodeEnum::PARTIAL_CONTENT,
        ResponseStatusCodeEnum::MULTI_STATUS,
        ResponseStatusCodeEnum::ALREADY_REPORTED,
        ResponseStatusCodeEnum::IM_USED
    ];
    const REDIRECTION = [
        ResponseStatusCodeEnum::MULTIPLE_CHOICES,
        ResponseStatusCodeEnum::MOVED_PERMANENTLY,
        ResponseStatusCodeEnum::FOUND,
        ResponseStatusCodeEnum::SEE_OTHER,
        ResponseStatusCodeEnum::NOT_MODIFIED,
        ResponseStatusCodeEnum::USE_PROXY,
        ResponseStatusCodeEnum::SWITCH_PROXY,
        ResponseStatusCodeEnum::TEMPORARY_REDIRECT,
        ResponseStatusCodeEnum::PERMANENT_REDIRECT
    ];
    const CLIENT_ERROR = [
        ResponseStatusCodeEnum::BAD_REQUEST,
        ResponseStatusCodeEnum::UNAUTHORIZED,
        ResponseStatusCodeEnum::PAYMENT_REQUIRED,
        ResponseStatusCodeEnum::FORBIDDEN,
        ResponseStatusCodeEnum::NOT_FOUND,
        ResponseStatusCodeEnum::METHOD_NOT_ALLOWED,
        ResponseStatusCodeEnum::NO_ACCEPTABLE,
        ResponseStatusCodeEnum::PROXY_AUTHENTICATION_REQUIRED,
        ResponseStatusCodeEnum::REQUEST_TIMEOUT,
        ResponseStatusCodeEnum::CONFLICT,
        ResponseStatusCodeEnum::GONE,
        ResponseStatusCodeEnum::LENGTH_REQUIRED,
        ResponseStatusCodeEnum::PRECONDITION_FAILED,
        ResponseStatusCodeEnum::PAYLOAD_TOO_LARGE,
        ResponseStatusCodeEnum::URI_TOO_LONG,
        ResponseStatusCodeEnum::UNSUPPORTED_MEDIA_TYPE,
        ResponseStatusCodeEnum::RANGE_NOT_SATISFIABLE,
        ResponseStatusCodeEnum::EXPECTATION_FAILED,
        ResponseStatusCodeEnum::IM_A_TEAPOT,
        ResponseStatusCodeEnum::MISDIRECTED_REQUEST,
        ResponseStatusCodeEnum::UNPROCESSABLE_ENTITY,
        ResponseStatusCodeEnum::LOCKED,
        ResponseStatusCodeEnum::FAILED_DEPENDENCY,
        ResponseStatusCodeEnum::UPGRADE_REQUIRED,
        ResponseStatusCodeEnum::PRECONDITION_REQUIRED,
        ResponseStatusCodeEnum::TOO_MANY_REQUESTS,
        ResponseStatusCodeEnum::REQUEST_HEADER_FIELDS_TOO_LARGE,
        ResponseStatusCodeEnum::UNAVAILABLE_FOR_LEGAL_REASONS
    ];
    const SERVER_ERROR = [
        ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR,
        ResponseStatusCodeEnum::NOT_IMPLEMENTED,
        ResponseStatusCodeEnum::BAD_GATEWAY,
        ResponseStatusCodeEnum::SERVICE_UNAVAILABLE,
        ResponseStatusCodeEnum::GATEWAY_TIMEOUT,
        ResponseStatusCodeEnum::HTTP_VERSION_NOT_SUPPORTED,
        ResponseStatusCodeEnum::VARIANT_ALSO_NEGOTIATES,
        ResponseStatusCodeEnum::INSUFFICIENT_STORAGE,
        ResponseStatusCodeEnum::LOOP_DETECTED,
        ResponseStatusCodeEnum::NOT_EXTENDED,
        ResponseStatusCodeEnum::NETWORK_AUTHENTICATION_REQUIRED
    ];
}