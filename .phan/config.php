<?php

declare(strict_types=1);

return [
    'target_php_version'                          => '8.2',
    'directory_list'                              => ['src/', 'vendor/'],
    'exclude_analysis_directory_list'             => ['vendor/'],
    'quick_mode'                                  => true,
    'analyze_signature_compatibility'             => true,
    'minimum_severity'                            => 0,
    'allow_missing_properties'                    => false,
    'null_casts_as_any_type'                      => false,
    'null_casts_as_array'                         => false,
    'array_casts_as_null'                         => false,
    'scalar_implicit_cast'                        => true, // TODO: Consider removing
    'scalar_implicit_partial'                     => [],
    'ignore_undeclared_variables_in_global_scope' => true, // TODO: No globals!
    'suppress_issue_types'                        => [
        'PhanTypeInvalidThrowsIsInterface',
        'PhanParamSignatureRealMismatchReturnType',
    ],
];
