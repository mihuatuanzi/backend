<?php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryList
{
    public function withRequest(
        Request $request,
        QueryBuilder $builder,
        array $sortingRuleMap = []
    ): QueryBuilder
    {
        $page = $request->get('page', 1);
        $pageSize = min($request->get('page_size', 36), 144);
        $sortingRule = $request->get('sorting_rule');

        if ($sortingRule && array_key_exists($sortingRule, $sortingRuleMap)) {
            $builder->orderBy($sortingRuleMap[$sortingRule]);
        }

        $builder->setFirstResult(($page - 1) * $pageSize);
        $builder->setMaxResults($pageSize);

        return $builder;
    }
}
