import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/catalog/brands',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CatalogController::destroy
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
export const destroy = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/catalog/brands/{brand}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CatalogController::destroy
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
destroy.url = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { brand: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { brand: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            brand: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.id
        : args.brand,
    }

    return destroy.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::destroy
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
destroy.delete = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CatalogController::destroy
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
const destroyForm = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::destroy
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
destroyForm.delete = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const brands = {
    store: Object.assign(store, store),
    destroy: Object.assign(destroy, destroy),
}

export default brands