<?php

#
#
# Parsedown
# http://parsedown.org
#
# (c) Emanuil Rusev
# http://erusev.com
#
# For the full license information, view the LICENSE file that was distributed
# with this source code.
#
#

class Parsedown
{
    # ~

    const version = '1.8.0-beta-7';

    # ~

    function text($text)
    {
        $Elements = $this->textElements($text);

        # convert to markup
        $markup = $this->elements($Elements);

        # trim line breaks
        $markup = trim($markup, "\n");

        return $markup;
    }

    protected function textElements($text)
    {
        # make sure no definitions are set
        $this->DefinitionData = array();

        # standardize line breaks
        $text = str_replace(array("\r\n", "\r"), "\n", $text);

        # remove surrounding line breaks
        $text = trim($text, "\n");

        # split text into lines
        $lines = explode("\n", $text);

        # iterate through lines to identify blocks
        return $this->linesElements($lines);
    }

    #
    # Setters
    #

    // function setBreaksEnabled($breaksEnabled)
    // {
    //     $this->breaksEnabled = $breaksEnabled;

    //     return $this;
    // }

    // protected $breaksEnabled; - нужно в function inlineText, я там поставил default True

    function setBreaksEnabled($breaksEnabled)
    {
        $this->breaksEnabled = $breaksEnabled;

        return $this;
    }

    protected $breaksEnabled = null;

    function setMarkupEscaped($markupEscaped)
    {
        $this->markupEscaped = $markupEscaped;

        return $this;
    }

    protected $markupEscaped = null;

    function setUrlsLinked($urlsLinked)
    {
        $this->urlsLinked = $urlsLinked;

        return $this;
    }

    protected $urlsLinked = true;

    function setSafeMode($safeMode)
    {
        $this->safeMode = (bool) $safeMode;

        return $this;
    }

    protected $safeMode = null;

    function setStrictMode($strictMode)
    {
        $this->strictMode = (bool) $strictMode;

        return $this;
    }

    protected $strictMode = null;

    protected $safeLinksWhitelist = array(
        'http://',
        'https://',
        'ftp://',
        'ftps://',
        'mailto:',
        'tel:',
        'data:image/png;base64,',
        'data:image/gif;base64,',
        'data:image/jpeg;base64,',
        'irc:',
        'ircs:',
        'git:',
        'ssh:',
        'news:',
        'steam:',
    );

    #
    # Lines
    #

    protected $BlockTypes = array(
        '#' => array('Header'),
        // '*' => array('Rule', 'List'),
        // '+' => array('List'),
        // '-' => array('SetextHeader', 'Table', 'Rule', 'List'),
        // '0' => array('List'),
        // '1' => array('List'),
        // '2' => array('List'),
        // '3' => array('List'),
        // '4' => array('List'),
        // '5' => array('List'),
        // '6' => array('List'),
        // '7' => array('List'),
        // '8' => array('List'),
        // '9' => array('List'),
        // ':' => array('Table'),
        // '<' => array('Comment', 'Markup'),
        // '=' => array('SetextHeader'),
        // '>' => array('Quote'),
        // '[' => array('Reference'),
        // '_' => array('Rule'),
        // '`' => array('FencedCode'),
        // '|' => array('Table'),
        // '~' => array('FencedCode'),
    );

    # ~

    protected $unmarkedBlockTypes = array(
        'Code',
    );


    #
    # Blocks - почти сделал
    #

    protected function lines(array $lines)
    {
        return $this->elements($this->linesElements($lines));
    }

    protected function linesElements(array $lines)
    {
        $Elements = array();
        $CurrentBlock = null;

        foreach ($lines as $line)
        {
            if (rtrim($line) === '')
            {
                if (isset($CurrentBlock))
                {
                    $CurrentBlock['interrupted'] = (isset($CurrentBlock['interrupted'])
                        ? $CurrentBlock['interrupted'] + 1 : 1
                    );
                }

                continue;
            }

            while (($beforeTab = strstr($line, "\t", true)) !== false)
            {
                $shortage = 4 - mb_strlen($beforeTab, 'utf-8') % 4;

                $line = $beforeTab
                    . str_repeat(' ', $shortage)
                    . substr($line, strlen($beforeTab) + 1)
                ;
            }

            $indent = strspn($line, ' ');

            $text = $indent > 0 ? substr($line, $indent) : $line;

            # ~

            $Line = array('body' => $line, 'indent' => $indent, 'text' => $text);

            # ~

            if (isset($CurrentBlock['continuable']))
            {
                # using switch instead
                // $methodName = 'block' . $CurrentBlock['type'] . 'Continue';
                // $Block = $this->$methodName($Line, $CurrentBlock);
                $Block = null;
                switch ($CurrentBlock['type']) {
                    case "Code":
                        if ($Line['indent'] >= 4) {
                            $Block = $this->$blockCodeContinue($Line, $CurrentBlock);
                        }
                        break;
                    // case "Comment":
                    //     $Block = $this->$blockCommentContinue($Line, $CurrentBlock);
                    //     break;
                    // case "FencedCode":
                    //     $Block = $this->$blockFencedCodeContinue($Line, $CurrentBlock);
                    //     break;
                    // case "List":
                    //     $Block = $this->$blockListContinue($Line, $CurrentBlock);
                    //     break;
                    // case "Quote":
                    //     $Block = $this->$blockQuoteContinue($Line, $CurrentBlock);
                    //     break;
                    // case "Markup":
                    //     $Block = $this->$blockMarkupContinue($Line, $CurrentBlock);
                    //     break;
                    // case "Table":
                    //     $Block = $this->$blockTableContinue($Line, $CurrentBlock);
                    //     break;
                }


                if (isset($Block))
                {
                    $CurrentBlock = $Block;

                    continue;
                }
                else
                {
                    # по факту тут был костыль, который проверял существуют ли такой метод.
                    # С помощью switch от него можно избавиться.
                    # using switch instead
                    // $methodName = 'block' . $CurrentBlock['type'] . 'Complete';
                    // $CurrentBlock = $this->$methodName($CurrentBlock);
                    switch ($CurrentBlock['type']) {
                        case "Code":
                            $Block = $this->$blockCodeComplete($CurrentBlock);
                            break;
                        // case "FencedCode":
                        //     $Block = $this->$blockFencedCodeComplete($CurrentBlock);
                        //     break;
                        // case "List":
                        //     $Block = $this->$blockListComplete($CurrentBlock);
                        //     break;
                    }
                }
            }

            # ~

            $marker = $text[0];

            # ~

            $blockTypes = $this->unmarkedBlockTypes;

            if (isset($this->BlockTypes[$marker]))
            {
                foreach ($this->BlockTypes[$marker] as $blockType)
                {
                    $blockTypes []= $blockType;
                }
            }
            #
            # ~

            foreach ($blockTypes as $blockType)
            {
                # using switch instead
                // $Block = $this->{"block$blockType"}($Line);
                $Block = null;
                switch ($blockType) {
                    case "Header":
                        $level = strspn($Line['text'], '#');

                        if ($level > 6)
                        {
                            break;
                        }

                        $text = trim($Line['text'], '#');

                        # тут добавил str_split - не лучшая идея
                        if ($this->strictMode and substr($text, 0, 1) !== ' ')
                        {
                            break;
                        }

                        $Block = $this->blockHeader($level, $text);

                        break;
                    // case "Comment":
                    //     $Block = $this->blockComment($Line);
                    //     break;
                    case "Code":
                        if (isset($CurrentBlock) and $CurrentBlock['type'] === 'Paragraph' and ! isset($CurrentBlock['interrupted']))
                        {
                            break;
                        }
                        if ($Line['indent'] >= 4){
                            $Block = $this->blockCode($Line);
                        }
                        $Block = null;
                        break;
                }

                if (isset($Block))
                {
                    $Block['type'] = $blockType;

                    if ( ! isset($Block['identified']))
                    {
                        if (isset($CurrentBlock))
                        {
                            $Elements[] = $this->extractElement($CurrentBlock);
                        }

                        $Block['identified'] = true;
                    }

                    # считаю это их костылём
                    // if ($this->isBlockContinuable($blockType))
                    // {
                    //     $Block['continuable'] = true;
                    // }

                    switch ($Block['type']) {
                        case "Code":
                            $Block['continuable'] = true;
                            break;
                        // case "Comment":
                        //     $Block['continuable'] = true;
                        //     break;
                        // case "FencedCode":
                        //     $Block['continuable'] = true;
                        //     break;
                        // case "List":
                        //     $Block['continuable'] = true;
                        //     break;
                        // case "Quote":
                        //     $Block['continuable'] = true;
                        //     break;
                        // case "Markup":
                        //     $Block['continuable'] = true;
                        //     break;
                        // case "Table":
                        //     $Block['continuable'] = true;
                        //     break;
                    }


                    $CurrentBlock = $Block;

                    continue 2;
                }
            }

            # ~

            if (isset($CurrentBlock) and $CurrentBlock['type'] === 'Paragraph' and !isset($CurrentBlock['interrupted']))
            {
                $Block = $this->paragraphContinue($Line, $CurrentBlock);
            }

            if (isset($Block))
            {
                $CurrentBlock = $Block;
            }
            else
            {
                if (isset($CurrentBlock))
                {
                    $Elements[] = $this->extractElement($CurrentBlock);
                }

                $CurrentBlock = $this->paragraph($Line);

                $CurrentBlock['identified'] = true;
            }
        }

        # ~

        if (isset($CurrentBlock['continuable']))
        {
            switch ($CurrentBlock['type']) {
                case "Code":
                    $Block = $this->blockCodeComplete($CurrentBlock);
                    break;
                // case "FencedCode":
                //     $Block = $this->$blockFencedCodeComplete($CurrentBlock);
                //     break;
                // case "List":
                //     $Block = $this->$blockListComplete($CurrentBlock);
                //     break;
            }
        }

        # ~

        if (isset($CurrentBlock))
        {
            $Elements[] = $this->extractElement($CurrentBlock);
        }

        # ~

        return $Elements;
    }

    protected function extractElement(array $Component)
    {
        if ( ! isset($Component['element']))
        {
            if (isset($Component['markup']))
            {
                $Component['element'] = array('rawHtml' => $Component['markup']);
            }
            elseif (isset($Component['hidden']))
            {
                $Component['element'] = array();
            }
        }

        return $Component['element'];
    }

    #
    # Code - сделал

    protected function blockCode($Line)
    {
        $text = substr($Line['body'], 4);

        $Block = array(
            'element' => array(
                'name' => 'pre',
                'element' => array(
                    'name' => 'code',
                    'text' => $text,
                ),
            ),
        );

        return $Block;
    }

    protected function blockCodeContinue($Line, $Block)
    {
        if (isset($Block['interrupted']))
        {
            $Block['element']['element']['text'] .= str_repeat("\n", $Block['interrupted']);

            unset($Block['interrupted']);
        }

        $Block['element']['element']['text'] .= "\n";

        $text = substr($Line['body'], 4);

        $Block['element']['element']['text'] .= $text;

        return $Block;
    }

    protected function blockCodeComplete($Block)
    {
        return $Block;
    }

    #
    # Comment

    #
    # Fenced Code

    #
    # Header - сделал

    protected function blockHeader($level, $text)
    {
        $text = trim($text, ' ');

        $Block = array(
            'element' => array(
                'name' => 'h' . $level,
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $text,
                    'destination' => 'elements',
                )
            ),
        );

        return $Block;
    }

    #
    # List

    #
    # Quote

    #
    # Rule

    #
    # Setext

    #
    # Markup

    #
    # Reference

    #
    # Table

    #
    # ~
    #

    protected function paragraph($Line)
    {
        return array(
            'type' => 'Paragraph',
            'element' => array(
                'name' => 'p',
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $Line['text'],
                    'destination' => 'elements',
                ),
            ),
        );
    }

    protected function paragraphContinue($Line, $Block)
    {
        $Block['element']['handler']['argument'] .= "\n".$Line['text'];

        return $Block;
    }

    #
    # Inline Elements
    #

    protected $InlineTypes = array(
        '!' => array('Image'),
        '&' => array('SpecialCharacter'),
        '*' => array('Emphasis'),
        ':' => array('Url'),
        '<' => array('UrlTag', 'EmailTag', 'Markup'),
        '[' => array('Link'),
        '_' => array('Emphasis'),
        '`' => array('Code'),
        '~' => array('Strikethrough'),
        '\\' => array('EscapeSequence'),
    );

    # ~

    protected $inlineMarkerList = '!*_&[:<`~\\';

    #
    # ~
    #

    protected function lineElements($text, $nonNestables = array())
    {
        # standardize line breaks
        $text = str_replace(array("\r\n", "\r"), "\n", $text);

        $Elements = array();

        $nonNestables = (empty($nonNestables)
            ? array()
            : array_combine($nonNestables, $nonNestables)
        );

        # $excerpt is based on the first occurrence of a marker

        while ($excerpt = strpbrk($text, $this->inlineMarkerList))
        {
            $marker = $excerpt[0];

            $markerPosition = strlen($text) - strlen($excerpt);

            $Excerpt = array('text' => $excerpt, 'context' => $text);

            foreach ($this->InlineTypes[$marker] as $inlineType)
            {
                # check to see if the current inline type is nestable in the current context

                if (isset($nonNestables[$inlineType]))
                {
                    continue;
                }

                # using switch instead
                // $Inline = $this->{"inline$inlineType"}($Excerpt);

                // if ( ! isset($Inline))
                // {
                //     continue;
                // }

                switch ($inlineType) {
                    // case "Image":
                    //     $Inline = $this->inlineImage($Excerpt);
                    //     break;
                    // case "SpecialCharacter":
                    //     $Inline = $this->inlineSpecialCharacter($Excerpt);
                    //     break;
                    // case "Emphasis":
                    //     $Inline = $this->inlineEmphasis($Excerpt);
                    //     break;
                    // case "Url":
                    //     $Inline = $this->inlineUrl($Excerpt);
                    //     break;
                    // case "UrlTag":
                    //     $Inline = $this->inlineUrlTag($Excerpt);
                    //     break;
                    // case "EmailTag":
                    //     $Inline = $this->inlineEmailTag($Excerpt);
                    //     break;
                    // case "Markup":
                    //     $Inline = $this->inlineMarkup($Excerpt);
                    //     break;
                    // case "Link":
                    //     $Inline = $this->inlineLink($Excerpt);
                    //     break;
                    // case "Emphasis":
                    //     $Inline = $this->inlineEmphasis($Excerpt);
                    //     break;
                    case "Code":
                        $marker = $Excerpt['text'][0];
                        if (preg_match('/^(['.$marker.']++)[ ]*+(.+?)[ ]*+(?<!['.$marker.'])\1(?!'.$marker.')/s', $Excerpt['text'], $matches))
                        {
                            $Inline = $this->inlineCode($matches);
                        }
                        break;
                    // case "Strikethrough":
                    //     $Inline = $this->inlineStrikethrough($Excerpt);
                    //     break;
                    // case "EscapeSequence":
                    //     $Inline = $this->inlineEscapeSequence($Excerpt);
                    //     break;
                    default:
                        continue;
                }

                # makes sure that the inline belongs to "our" marker

                if (isset($Inline['position']) and $Inline['position'] > $markerPosition)
                {
                    continue;
                }

                # sets a default inline position

                if ( ! isset($Inline['position']))
                {
                    $Inline['position'] = $markerPosition;
                }

                # cause the new element to 'inherit' our non nestables


                $Inline['element']['nonNestables'] = isset($Inline['element']['nonNestables'])
                    ? array_merge($Inline['element']['nonNestables'], $nonNestables)
                    : $nonNestables
                ;

                # the text that comes before the inline
                $unmarkedText = substr($text, 0, $Inline['position']);

                # compile the unmarked text
                $InlineText = $this->inlineText($unmarkedText);
                $Elements[] = $InlineText['element'];

                # compile the inline
                $Elements[] = $this->extractElement($Inline);

                # remove the examined text
                $text = substr($text, $Inline['position'] + $Inline['extent']);

                continue 2;
            }

            # the marker does not belong to an inline

            $unmarkedText = substr($text, 0, $markerPosition + 1);

            $InlineText = $this->inlineText($unmarkedText);
            $Elements[] = $InlineText['element'];

            $text = substr($text, $markerPosition + 1);
        }

        $InlineText = $this->inlineText($text);
        $Elements[] = $InlineText['element'];

        foreach ($Elements as &$Element)
        {
            if ( ! isset($Element['autobreak']))
            {
                $Element['autobreak'] = false;
            }
        }

        return $Elements;
    }

    #
    # ~
    #

    protected function inlineText($text)
    {
        $Inline = array(
            'extent' => strlen($text),
            'element' => array(),
        );

        $Inline['element']['elements'] = self::pregReplaceElements(
        // $this->breaksEnabled ? '/[ ]*+\n/' : '/(?:[ ]*+\\\\|[ ]{2,}+)\n/',
            '/[ ]*+\n/',
            array(
                array('name' => 'br'),
                array('text' => "\n"),
            ),
            $text
        );

        return $Inline;
    }

    protected function inlineCode($matches)
    {
        $text = $matches[2];
        $text = preg_replace('/[ ]*+\n/', ' ', $text);

        return array(
            'extent' => strlen($matches[0]),
            'element' => array(
                'name' => 'code',
                'text' => $text,
            ),
        );
    }

    #
    # Handlers
    #

    protected function handle($Element)
    {
        if (isset($Element['handler']))
        {
            if (!isset($Element['nonNestables']))
            {
                $Element['nonNestables'] = array();
            }

            if (is_string($Element['handler']))
            {
                $function = $Element['handler'];
                $argument = $Element['text'];
                unset($Element['text']);
                $destination = 'rawHtml';
            }
            else
            {
                $function = $Element['handler']['function'];
                $argument = $Element['handler']['argument'];
                $destination = $Element['handler']['destination'];
            }

            #Using switch instead
            // $Element[$destination] = $this->{$function}($argument, $Element['nonNestables']);

            switch ($function) {
                case "lineElements":
                    $Element[$destination] = $this->lineElements($argument, $Element['nonNestables']);
                    break;
                // case "li":
                //     $Element[$destination] = $this->li($argument);
                //     break;
                // case "linesElements":
                //     $Element[$destination] = $this->linesElements($argument);
                //     break;
            }

            if ($destination === 'handler')
            {
                $Element = $this->handle($Element);
            }

            unset($Element['handler']);
        }

        return $Element;
    }

    protected function element($Element)
    {
        if ($this->safeMode)
        {
            $Element = $this->sanitiseElement($Element);
        }

        # identity map if element has no handler
        $Element = $this->handle($Element);

        $hasName = isset($Element['name']);

        $markup = '';

        if ($hasName)
        {
            $markup .= '<' . $Element['name'];

            if (isset($Element['attributes']))
            {
                foreach ($Element['attributes'] as $name => $value)
                {
                    if ($value === null)
                    {
                        continue;
                    }

                    $markup .= " $name=\"".self::escape($value).'"';
                }
            }
        }

        $permitRawHtml = false;

        if (isset($Element['text']))
        {
            $text = $Element['text'];
        }
        // very strongly consider an alternative if you're writing an
        // extension
        elseif (isset($Element['rawHtml']))
        {
            $text = $Element['rawHtml'];

            $allowRawHtmlInSafeMode = isset($Element['allowRawHtmlInSafeMode']) && $Element['allowRawHtmlInSafeMode'];
            $permitRawHtml = !$this->safeMode || $allowRawHtmlInSafeMode;
        }

        $hasContent = isset($text) || isset($Element['element']) || isset($Element['elements']);

        if ($hasContent)
        {
            $markup .= $hasName ? '>' : '';

            if (isset($Element['elements']))
            {
                $markup .= $this->elements($Element['elements']);
            }
            elseif (isset($Element['element']))
            {
                $markup .= $this->element($Element['element']);
            }
            else
            {
                if (!$permitRawHtml)
                {
                    $markup .= self::escape($text, true);
                }
                else
                {
                    $markup .= $text;
                }
            }

            $markup .= $hasName ? '</' . $Element['name'] . '>' : '';
        }
        elseif ($hasName)
        {
            $markup .= ' />';
        }

        return $markup;
    }

    protected function elements($Elements)
    {
        $markup = '';

        $autoBreak = true;

        foreach ($Elements as $Element)
        {
            if (empty($Element))
            {
                continue;
            }

            $autoBreakNext = (isset($Element['autobreak'])
                ? $Element['autobreak'] : isset($Element['name'])
            );
            // (autobreak === false) covers both sides of an element
            $autoBreak = !$autoBreak ? $autoBreak : $autoBreakNext;

            $markup .= ($autoBreak ? "\n" : '') . $this->element($Element);
            $autoBreak = $autoBreakNext;
        }

        $markup .= $autoBreak ? "\n" : '';
        return $markup;
    }

    #
    # AST Convenience
    #

    /**
     * Replace occurrences $regexp with $Elements in $text. Return an array of
     * elements representing the replacement.
     */
    protected static function pregReplaceElements($regexp, $Elements, $text)
    {
        $newElements = array();

        while (preg_match($regexp, $text, $matches, PREG_OFFSET_CAPTURE))
        {
            $offset = $matches[0][1];
            $before = substr($text, 0, $offset);
            $after = substr($text, $offset + strlen($matches[0][0]));

            $newElements[] = array('text' => $before);

            foreach ($Elements as $Element)
            {
                $newElements[] = $Element;
            }

            $text = $after;
        }

        $newElements[] = array('text' => $text);

        return $newElements;
    }

    #
    # Deprecated Methods
    #

    protected function sanitiseElement($Element)
    {
        static $goodAttribute = '/^[a-zA-Z0-9][a-zA-Z0-9-_]*+$/';
        static $safeUrlNameToAtt  = array(
            'a'   => 'href',
            'img' => 'src',
        );

        if ( ! isset($Element['name']))
        {
            unset($Element['attributes']);
            return $Element;
        }

        if (isset($safeUrlNameToAtt[$Element['name']]))
        {
            $Element = $this->filterUnsafeUrlInAttribute($Element, $safeUrlNameToAtt[$Element['name']]);
        }

        if ( ! empty($Element['attributes']))
        {
            foreach ($Element['attributes'] as $att => $val)
            {
                # filter out badly parsed attribute
                if ( ! preg_match($goodAttribute, $att))
                {
                    unset($Element['attributes'][$att]);
                }
                # dump onevent attribute
                elseif (self::striAtStart($att, 'on'))
                {
                    unset($Element['attributes'][$att]);
                }
            }
        }

        return $Element;
    }

    protected function filterUnsafeUrlInAttribute($Element, $attribute)
    {
        foreach ($this->safeLinksWhitelist as $scheme)
        {
            if (self::striAtStart($Element['attributes'][$attribute], $scheme))
            {
                return $Element;
            }
        }

        // $Element['attributes'][$attribute] = str_replace(':', '%3A', $Element['attributes'][$attribute]);

        return $Element;
    }


    #
    # Static Methods
    #

    # I need to remove UTF-8
    // return htmlspecialchars($text, $allowQuotes ? ENT_NOQUOTES : ENT_QUOTES, 'UTF-8');
    protected static function escape($text, $allowQuotes = false)
    {
        return htmlspecialchars($text, $allowQuotes ? ENT_NOQUOTES : ENT_QUOTES);
    }

    protected static function striAtStart($string, $needle)
    {
        $len = strlen($needle);

        if ($len > strlen($string))
        {
            return false;
        }
        else
        {
            return strtolower(substr($string, 0, $len)) === strtolower($needle);
        }
    }

    #
    # Fields
    #

    protected $DefinitionData;
}

$parsedown = new Parsedown();

$text = file_get_contents('article.txt');

var_dump($parsedown->text($text));