import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PosController::index
* @see app/Http/Controllers/PosController.php:23
* @route '/pos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/pos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PosController::index
* @see app/Http/Controllers/PosController.php:23
* @route '/pos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PosController::index
* @see app/Http/Controllers/PosController.php:23
* @route '/pos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PosController::index
* @see app/Http/Controllers/PosController.php:23
* @route '/pos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PosController::index
* @see app/Http/Controllers/PosController.php:23
* @route '/pos'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PosController::index
* @see app/Http/Controllers/PosController.php:23
* @route '/pos'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PosController::index
* @see app/Http/Controllers/PosController.php:23
* @route '/pos'
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
* @see \App\Http\Controllers\PosController::products
* @see app/Http/Controllers/PosController.php:47
* @route '/pos/products'
*/
export const products = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: products.url(options),
    method: 'get',
})

products.definition = {
    methods: ["get","head"],
    url: '/pos/products',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PosController::products
* @see app/Http/Controllers/PosController.php:47
* @route '/pos/products'
*/
products.url = (options?: RouteQueryOptions) => {
    return products.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PosController::products
* @see app/Http/Controllers/PosController.php:47
* @route '/pos/products'
*/
products.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: products.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PosController::products
* @see app/Http/Controllers/PosController.php:47
* @route '/pos/products'
*/
products.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: products.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PosController::products
* @see app/Http/Controllers/PosController.php:47
* @route '/pos/products'
*/
const productsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: products.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PosController::products
* @see app/Http/Controllers/PosController.php:47
* @route '/pos/products'
*/
productsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: products.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PosController::products
* @see app/Http/Controllers/PosController.php:47
* @route '/pos/products'
*/
productsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: products.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

products.form = productsForm

/**
* @see \App\Http\Controllers\PosController::checkout
* @see app/Http/Controllers/PosController.php:93
* @route '/pos/checkout'
*/
export const checkout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

checkout.definition = {
    methods: ["post"],
    url: '/pos/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PosController::checkout
* @see app/Http/Controllers/PosController.php:93
* @route '/pos/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PosController::checkout
* @see app/Http/Controllers/PosController.php:93
* @route '/pos/checkout'
*/
checkout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PosController::checkout
* @see app/Http/Controllers/PosController.php:93
* @route '/pos/checkout'
*/
const checkoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: checkout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PosController::checkout
* @see app/Http/Controllers/PosController.php:93
* @route '/pos/checkout'
*/
checkoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: checkout.url(options),
    method: 'post',
})

checkout.form = checkoutForm

/**
* @see \App\Http\Controllers\PosController::hold
* @see app/Http/Controllers/PosController.php:100
* @route '/pos/hold'
*/
export const hold = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: hold.url(options),
    method: 'post',
})

hold.definition = {
    methods: ["post"],
    url: '/pos/hold',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PosController::hold
* @see app/Http/Controllers/PosController.php:100
* @route '/pos/hold'
*/
hold.url = (options?: RouteQueryOptions) => {
    return hold.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PosController::hold
* @see app/Http/Controllers/PosController.php:100
* @route '/pos/hold'
*/
hold.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: hold.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PosController::hold
* @see app/Http/Controllers/PosController.php:100
* @route '/pos/hold'
*/
const holdForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: hold.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PosController::hold
* @see app/Http/Controllers/PosController.php:100
* @route '/pos/hold'
*/
holdForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: hold.url(options),
    method: 'post',
})

hold.form = holdForm

const PosController = { index, products, checkout, hold }

export default PosController