<?php

namespace Greywizard\Phpucd\Tokens;

class Tokenizer
{
    /**
     * @param \SplFileInfo|string $file
     * @return TokenInterface[]|\Traversable
     */
    public static function createFromFile($file)
    {
        return static::createFromString(file_get_contents(
            is_string($file) ? $file : $file->getRealPath()
        ));
    }

    /**
     * @param string $input
     * @return TokenInterface[]|\Traversable
     */
    public static function createFromString($input)
    {
        $line = 1;
        foreach (token_get_all($input) as $rawToken) {
            if (is_string($rawToken)) {
                yield new StringToken($line, $rawToken);
                $line += substr_count($rawToken, "\n");
                continue;
            }

            list($token, $source, $line) = $rawToken;

            yield new Token($line, $token, $source);
        }
    }
}
