<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use League\Csv\Reader;

/**
 * Class RedirectManager
 *
 * @package Adrenth\Redirect\Classes
 */
class RedirectManager
{
    /** @type string */
    private $redirectRulesPath;

    /** @type RedirectRule[] */
    protected $redirectRules;

    /**
     * @param string $redirectRulesPath
     */
    public function __construct($redirectRulesPath)
    {
        $this->redirectRulesPath = $redirectRulesPath;
    }

    /**
     * Find a match based on given URL
     *
     * @param string $url
     * @return RedirectRule|false
     */
    public function match($url)
    {
        $this->loadRedirectRules();

        foreach ($this->redirectRules as $rule) {
            if ($this->matchesRule($rule, $url)) {
                return $rule;
            }
        }

        return false;
    }

    /**
     * Redirect with specific rule
     *
     * @param RedirectRule $rule
     * @return void
     */
    public function redirectWithRule(RedirectRule $rule)
    {
        try {
            /** @type Redirect $redirect */
            $redirect = Redirect::find($rule->getId());
            $redirect->setAttribute('hits', $redirect->getAttribute('hits') + 1);
            $redirect->save();
        } catch (\Exception $e) {

        }

        header('Location: ' . $rule->getToUrl(), true, $rule->getStatusCode());

        exit();
    }

    /**
     * @param RedirectRule $rule
     * @param string $url
     * @return bool
     */
    private function matchesRule(RedirectRule $rule, $url)
    {
        switch ($rule->getMatchType()) {
            case Redirect::TYPE_EXACT:
                return $url === $rule->getFromUrl();
            case Redirect::TYPE_REGEX:
                // TODO implement
                return false;
        }
    }

    /**
     * Load definitions into memory
     *
     * @return RedirectRule[]
     */
    private function loadRedirectRules()
    {
        if ($this->redirectRules !== null) {
            return;
        }

        $rules = [];
        $reader = Reader::createFromPath($this->redirectRulesPath);

        // TODO php5.5+ yield?

        foreach ($reader as $row) {
            $rules[] = new RedirectRule($row);
        }

        $this->redirectRules = $rules;
    }
}
