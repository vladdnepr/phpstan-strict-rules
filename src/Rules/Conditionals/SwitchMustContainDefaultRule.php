<?php


namespace TheCodingMachine\PHPStan\Rules\Conditionals;

use PhpParser\Node;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use TheCodingMachine\PHPStan\Utils\PrefixGenerator;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleError;

/**
 * A switch statement must always contain a "default" statement.
 *
 * @implements Rule<Switch_>
 */
class SwitchMustContainDefaultRule implements Rule
{
    public function getNodeType(): string
    {
        return Switch_::class;
    }

    /**
     * @param Switch_ $switch
     * @param \PHPStan\Analyser\Scope $scope
     * @return RuleError[]
     */
    public function processNode(Node $switch, Scope $scope): array
    {
        $errors = [];
        $defaultFound = false;
        foreach ($switch->cases as $case) {
            if ($case->cond === null) {
                $defaultFound = true;
                break;
            }
        }

        if (!$defaultFound) {
            $errors[] = RuleErrorBuilder::message(
                sprintf(PrefixGenerator::generatePrefix($scope).'switch statement does not have a "default" case. If your code is supposed to enter at least one "case" or another, consider adding a "default" case that throws an exception. More info: http://bit.ly/switchdefault')
            )->identifier('cm.switch.default')->build();
        }

        return $errors;
    }
}
