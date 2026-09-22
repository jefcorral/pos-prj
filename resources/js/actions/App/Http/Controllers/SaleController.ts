import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SaleController::index
* @see app/Http/Controllers/SaleController.php:19
* @route '/sales'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/sales',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SaleController::index
* @see app/Http/Controllers/SaleController.php:19
* @route '/sales'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SaleController::index
* @see app/Http/Controllers/SaleController.php:19
* @route '/sales'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::index
* @see app/Http/Controllers/SaleController.php:19
* @route '/sales'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SaleController::index
* @see app/Http/Controllers/SaleController.php:19
* @route '/sales'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::index
* @see app/Http/Controllers/SaleController.php:19
* @route '/sales'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::index
* @see app/Http/Controllers/SaleController.php:19
* @route '/sales'
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
* @see \App\Http\Controllers\SaleController::show
* @see app/Http/Controllers/SaleController.php:43
* @route '/sales/{sale}'
*/
export const show = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/sales/{sale}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SaleController::show
* @see app/Http/Controllers/SaleController.php:43
* @route '/sales/{sale}'
*/
show.url = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sale: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sale: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sale: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sale: typeof args.sale === 'object'
        ? args.sale.id
        : args.sale,
    }

    return show.definition.url
            .replace('{sale}', parsedArgs.sale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SaleController::show
* @see app/Http/Controllers/SaleController.php:43
* @route '/sales/{sale}'
*/
show.get = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::show
* @see app/Http/Controllers/SaleController.php:43
* @route '/sales/{sale}'
*/
show.head = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SaleController::show
* @see app/Http/Controllers/SaleController.php:43
* @route '/sales/{sale}'
*/
const showForm = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::show
* @see app/Http/Controllers/SaleController.php:43
* @route '/sales/{sale}'
*/
showForm.get = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::show
* @see app/Http/Controllers/SaleController.php:43
* @route '/sales/{sale}'
*/
showForm.head = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\SaleController::receipt
* @see app/Http/Controllers/SaleController.php:67
* @route '/sales/{sale}/receipt'
*/
export const receipt = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: receipt.url(args, options),
    method: 'get',
})

receipt.definition = {
    methods: ["get","head"],
    url: '/sales/{sale}/receipt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SaleController::receipt
* @see app/Http/Controllers/SaleController.php:67
* @route '/sales/{sale}/receipt'
*/
receipt.url = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sale: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sale: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sale: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sale: typeof args.sale === 'object'
        ? args.sale.id
        : args.sale,
    }

    return receipt.definition.url
            .replace('{sale}', parsedArgs.sale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SaleController::receipt
* @see app/Http/Controllers/SaleController.php:67
* @route '/sales/{sale}/receipt'
*/
receipt.get = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: receipt.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::receipt
* @see app/Http/Controllers/SaleController.php:67
* @route '/sales/{sale}/receipt'
*/
receipt.head = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: receipt.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SaleController::receipt
* @see app/Http/Controllers/SaleController.php:67
* @route '/sales/{sale}/receipt'
*/
const receiptForm = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receipt.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::receipt
* @see app/Http/Controllers/SaleController.php:67
* @route '/sales/{sale}/receipt'
*/
receiptForm.get = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receipt.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SaleController::receipt
* @see app/Http/Controllers/SaleController.php:67
* @route '/sales/{sale}/receipt'
*/
receiptForm.head = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receipt.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

receipt.form = receiptForm

/**
* @see \App\Http\Controllers\SaleController::voidMethod
* @see app/Http/Controllers/SaleController.php:79
* @route '/sales/{sale}/void'
*/
export const voidMethod = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: voidMethod.url(args, options),
    method: 'post',
})

voidMethod.definition = {
    methods: ["post"],
    url: '/sales/{sale}/void',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SaleController::voidMethod
* @see app/Http/Controllers/SaleController.php:79
* @route '/sales/{sale}/void'
*/
voidMethod.url = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sale: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sale: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sale: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sale: typeof args.sale === 'object'
        ? args.sale.id
        : args.sale,
    }

    return voidMethod.definition.url
            .replace('{sale}', parsedArgs.sale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SaleController::voidMethod
* @see app/Http/Controllers/SaleController.php:79
* @route '/sales/{sale}/void'
*/
voidMethod.post = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: voidMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SaleController::voidMethod
* @see app/Http/Controllers/SaleController.php:79
* @route '/sales/{sale}/void'
*/
const voidMethodForm = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: voidMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SaleController::voidMethod
* @see app/Http/Controllers/SaleController.php:79
* @route '/sales/{sale}/void'
*/
voidMethodForm.post = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: voidMethod.url(args, options),
    method: 'post',
})

voidMethod.form = voidMethodForm

/**
* @see \App\Http\Controllers\SaleController::refund
* @see app/Http/Controllers/SaleController.php:91
* @route '/sales/{sale}/refund'
*/
export const refund = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refund.url(args, options),
    method: 'post',
})

refund.definition = {
    methods: ["post"],
    url: '/sales/{sale}/refund',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SaleController::refund
* @see app/Http/Controllers/SaleController.php:91
* @route '/sales/{sale}/refund'
*/
refund.url = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sale: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sale: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sale: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sale: typeof args.sale === 'object'
        ? args.sale.id
        : args.sale,
    }

    return refund.definition.url
            .replace('{sale}', parsedArgs.sale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SaleController::refund
* @see app/Http/Controllers/SaleController.php:91
* @route '/sales/{sale}/refund'
*/
refund.post = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refund.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SaleController::refund
* @see app/Http/Controllers/SaleController.php:91
* @route '/sales/{sale}/refund'
*/
const refundForm = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refund.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SaleController::refund
* @see app/Http/Controllers/SaleController.php:91
* @route '/sales/{sale}/refund'
*/
refundForm.post = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refund.url(args, options),
    method: 'post',
})

refund.form = refundForm

/**
* @see \App\Http\Controllers\SaleController::destroyHeld
* @see app/Http/Controllers/SaleController.php:109
* @route '/sales/{sale}/held'
*/
export const destroyHeld = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyHeld.url(args, options),
    method: 'delete',
})

destroyHeld.definition = {
    methods: ["delete"],
    url: '/sales/{sale}/held',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SaleController::destroyHeld
* @see app/Http/Controllers/SaleController.php:109
* @route '/sales/{sale}/held'
*/
destroyHeld.url = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { sale: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { sale: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            sale: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        sale: typeof args.sale === 'object'
        ? args.sale.id
        : args.sale,
    }

    return destroyHeld.definition.url
            .replace('{sale}', parsedArgs.sale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SaleController::destroyHeld
* @see app/Http/Controllers/SaleController.php:109
* @route '/sales/{sale}/held'
*/
destroyHeld.delete = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyHeld.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\SaleController::destroyHeld
* @see app/Http/Controllers/SaleController.php:109
* @route '/sales/{sale}/held'
*/
const destroyHeldForm = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyHeld.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SaleController::destroyHeld
* @see app/Http/Controllers/SaleController.php:109
* @route '/sales/{sale}/held'
*/
destroyHeldForm.delete = (args: { sale: number | { id: number } } | [sale: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyHeld.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyHeld.form = destroyHeldForm

const SaleController = { index, show, receipt, voidMethod, refund, destroyHeld, void: voidMethod }

export default SaleController