<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AtLeastOneRequired extends Constraint
{
    public array $requiredFields;

    public string $message = 'At least one of {{ fields }} is required.';

    public const ONE_REQUIRED_ERROR = 'de86ec96-cffe-49eb-b915-7e8f902284f9';

    protected static $errorNames = [
        self::ONE_REQUIRED_ERROR => 'ONE_REQUIRED_ERROR',
    ];

    public function __construct(
        mixed $options = [],
        ?array $requiredFields = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        if (!empty($options) && array_is_list($options)) {
            $requiredFields = $requiredFields ?? $options;
            $options = [];
        }

        if (empty($requiredFields)) {
            throw new ConstraintDefinitionException('The "requiredFields" of AtLeastOneRequired constraint');
        }

        $options['value'] = $requiredFields;

        parent::__construct($options, $groups, $payload);

        $this->requiredFields = $requiredFields;
        $this->message = $message ?? $this->message;
    }

    public function getRequiredOptions(): array
    {
        return ['requiredFields'];
    }

    public function getDefaultOption(): string
    {
        return 'requiredFields';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
