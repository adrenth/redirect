<?php

namespace Adrenth\Redirect\Classes;

use Adrenth\Redirect\Models\Redirect;
use Carbon\Carbon;
use InvalidArgumentException;
use League\Csv\Reader;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

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
    private $redirectRules;

    /** @type Carbon */
    private $matchDate;

    /**
     * Constructs a RedirectManager instance
     */
    protected function __construct()
    {
        $this->matchDate = Carbon::now();
    }

    /**
     * @param $redirectRulesPath
     * @return RedirectManager
     */
    public static function createWithRulesPath($redirectRulesPath)
    {
        $instance = new self();
        $instance->redirectRulesPath = $redirectRulesPath;
        return $instance;
    }

    /**
     * @param RedirectRule $rule
     * @return RedirectManager
     */
    public static function createWithRule(RedirectRule $rule)
    {
        $instance = new self();
        $instance->redirectRules[] = $rule;
        return $instance;
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
            if ($matchedRule = $this->matchesRule($rule, $url)) {
                return $matchedRule;
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
     * @param Carbon $matchDate
     * @return $this
     */
    public function setMatchDate(Carbon $matchDate)
    {
        $this->matchDate = $matchDate;
        return $this;
    }

    /**
     * @param RedirectRule $rule
     * @param string $url
     * @return bool
     */
    private function matchesRule(RedirectRule $rule, $url)
    {
        // Perform a period match first!
        if (!$this->matchesPeriod($rule)) {
            return false;
        }

        // TODO: Refactor
        switch ($rule->getMatchType()) {
            case Redirect::TYPE_EXACT:
                return $url === $rule->getFromUrl() ? $rule : false;
            case Redirect::TYPE_PLACEHOLDERS:
                $route = new Route($rule->getFromUrl());

                foreach ($rule->getRequirements() as $requirement) {
                    try {
                        $route->setRequirement(
                            str_replace(['{', '}'], '', $requirement['placeholder']),
                            $requirement['requirement']
                        );
                    } catch (\InvalidArgumentException $e) {
                        // Catch empty requirement / placeholder
                    }
                }

                $routeCollection = new RouteCollection();
                $routeCollection->add($rule->getId(), $route);

                try {
                    $matcher = new UrlMatcher($routeCollection, new RequestContext('/'));
                    $match = $matcher->match($url);

                    $items = array_except($match, '_route');

                    foreach ($items as $key => $value) {
                        $placeholder = '{' . $key . '}';
                        $replacement = $this->findReplacementForPlaceholder($rule, $placeholder);
                        $items[$placeholder] = $replacement === null ? $value : $replacement;
                        unset($items[$key]);
                    }

                    $toUrl = str_replace(
                        array_keys($items),
                        array_values($items),
                        $rule->getToUrl()
                    );
                } catch (\Exception $e) {
                    return false;
                }

                return new RedirectRule([
                    $rule->getId(),
                    $rule->getMatchType(),
                    $rule->getFromUrl(),
                    $toUrl,
                    $rule->getStatusCode(),
                    json_encode($rule->getRequirements()),
                    $rule->getFromDate(),
                    $rule->getToDate(),
                ]);
        }

        return false;
    }

    /**
     * Check if rule matches a period
     *
     * @param RedirectRule $rule
     * @return bool
     */
    private function matchesPeriod(RedirectRule $rule)
    {
        /** @type Carbon $fromDate */
        $fromDate = ($rule->getFromDate() instanceof Carbon) ? $rule->getFromDate() : clone $this->matchDate;
        /** @type Carbon $toDate */
        $toDate = ($rule->getToDate() instanceof Carbon) ? $rule->getToDate() : clone $this->matchDate;

        return $this->matchDate->between($fromDate, $toDate);
    }

    /**
     * Find replacement value for placeholder
     *
     * @param RedirectRule $rule
     * @param string $placeholder
     * @return string|null
     */
    private function findReplacementForPlaceholder(RedirectRule $rule, $placeholder)
    {
        foreach ($rule->getRequirements() as $requirement) {
            if ($requirement['placeholder'] === $placeholder && !empty($requirement['replacement'])) {
                return (string) $requirement['replacement'];
            }
        }

        return null;
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

        try {
            $reader = Reader::createFromPath($this->redirectRulesPath);

            foreach ($reader as $row) {
                $rules[] = new RedirectRule($row);
            }
        } catch (\Exception $e) {
            trace_log($e->getMessage(), 'error');
        }

        $this->redirectRules = $rules;
    }
}
