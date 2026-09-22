import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import categories from './categories'
import brands from './brands'
import units from './units'
import taxes from './taxes'
import discounts from './discounts'
/**
* @see \App\Http\Controllers\CatalogController::index
* @see app/Http/Controllers/CatalogController.php:22
* @route '/catalog'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/catalog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CatalogController::index
* @see app/Http/Controllers/CatalogController.php:22
* @route '/catalog'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::index
* @see app/Http/Controllers/CatalogController.php:22
* @route '/catalog'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CatalogController::index
* @see app/Http/Controllers/CatalogController.php:22
* @route '/catalog'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CatalogController::index
* @see app/Http/Controllers/CatalogController.php:22
* @route '/catalog'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CatalogController::index
* @see app/Http/Controllers/CatalogController.php:22
* @route '/catalog'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CatalogController::index
* @see app/Http/Controllers/CatalogController.php:22
* @route '/catalog'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const catalog = {
    index: Object.assign(index, index),
    categories: Object.assign(categories, categories),
    brands: Object.assign(brands, brands),
    units: Object.assign(units, units),
    taxes: Object.assign(taxes, taxes),
    discounts: Object.assign(discounts, discounts),
}

export default catalog