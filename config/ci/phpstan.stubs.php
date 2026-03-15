<?php

declare(strict_types=1);

namespace PHP_CodeSniffer\Files;

class File
{
    /**
     * @return array<int, array{
     *     code: int|string,
     *     type: string,
     *     content: string,
     *     line: int,
     *     column: int,
     *     level: int,
     *     scope_opener?: int,
     *     scope_closer?: int,
     *     scope_condition?: int,
     *     bracket_opener?: int,
     *     bracket_closer?: int,
     *     parenthesis_opener?: int,
     *     parenthesis_closer?: int,
     *     parenthesis_owner?: int,
     *     conditions?: array<int, int>,
     *     nested_parenthesis?: array<int, int>,
     *     comment_closer?: int,
     *     length: int,
     * }>
     */
    public function getTokens(): array
    {
    }
}
