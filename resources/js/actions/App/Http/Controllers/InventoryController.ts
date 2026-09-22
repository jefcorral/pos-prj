import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:17
* @route '/inventory'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/inventory',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:17
* @route '/inventory'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:17
* @route '/inventory'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:17
* @route '/inventory'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:17
* @route '/inventory'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:17
* @route '/inventory'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:17
* @route '/inventory'
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

/**
* @see \App\Http\Controllers\InventoryController::movements
* @see app/Http/Controllers/InventoryController.php:46
* @route '/inventory/movements'
*/
export const movements = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: movements.url(options),
    method: 'get',
})

movements.definition = {
    methods: ["get","head"],
    url: '/inventory/movements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::movements
* @see app/Http/Controllers/InventoryController.php:46
* @route '/inventory/movements'
*/
movements.url = (options?: RouteQueryOptions) => {
    return movements.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::movements
* @see app/Http/Controllers/InventoryController.php:46
* @route '/inventory/movements'
*/
movements.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: movements.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::movements
* @see app/Http/Controllers/InventoryController.php:46
* @route '/inventory/movements'
*/
movements.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: movements.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InventoryController::movements
* @see app/Http/Controllers/InventoryController.php:46
* @route '/inventory/movements'
*/
const movementsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: movements.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::movements
* @see app/Http/Controllers/InventoryController.php:46
* @route '/inventory/movements'
*/
movementsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: movements.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::movements
* @see app/Http/Controllers/InventoryController.php:46
* @route '/inventory/movements'
*/
movementsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: movements.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

movements.form = movementsForm

/**
* @see \App\Http\Controllers\InventoryController::adjust
* @see app/Http/Controllers/InventoryController.php:68
* @route '/inventory/adjust'
*/
export const adjust = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: adjust.url(options),
    method: 'post',
})

adjust.definition = {
    methods: ["post"],
    url: '/inventory/adjust',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InventoryController::adjust
* @see app/Http/Controllers/InventoryController.php:68
* @route '/inventory/adjust'
*/
adjust.url = (options?: RouteQueryOptions) => {
    return adjust.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::adjust
* @see app/Http/Controllers/InventoryController.php:68
* @route '/inventory/adjust'
*/
adjust.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: adjust.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InventoryController::adjust
* @see app/Http/Controllers/InventoryController.php:68
* @route '/inventory/adjust'
*/
const adjustForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: adjust.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InventoryController::adjust
* @see app/Http/Controllers/InventoryController.php:68
* @route '/inventory/adjust'
*/
adjustForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: adjust.url(options),
    method: 'post',
})

adjust.form = adjustForm

const InventoryController = { index, movements, adjust }

export default InventoryController