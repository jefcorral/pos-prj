import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PurchaseOrderController::index
* @see app/Http/Controllers/PurchaseOrderController.php:20
* @route '/purchase-orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/purchase-orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PurchaseOrderController::index
* @see app/Http/Controllers/PurchaseOrderController.php:20
* @route '/purchase-orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PurchaseOrderController::index
* @see app/Http/Controllers/PurchaseOrderController.php:20
* @route '/purchase-orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::index
* @see app/Http/Controllers/PurchaseOrderController.php:20
* @route '/purchase-orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::index
* @see app/Http/Controllers/PurchaseOrderController.php:20
* @route '/purchase-orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::index
* @see app/Http/Controllers/PurchaseOrderController.php:20
* @route '/purchase-orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::index
* @see app/Http/Controllers/PurchaseOrderController.php:20
* @route '/purchase-orders'
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
* @see \App\Http\Controllers\PurchaseOrderController::create
* @see app/Http/Controllers/PurchaseOrderController.php:38
* @route '/purchase-orders/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/purchase-orders/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PurchaseOrderController::create
* @see app/Http/Controllers/PurchaseOrderController.php:38
* @route '/purchase-orders/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PurchaseOrderController::create
* @see app/Http/Controllers/PurchaseOrderController.php:38
* @route '/purchase-orders/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::create
* @see app/Http/Controllers/PurchaseOrderController.php:38
* @route '/purchase-orders/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::create
* @see app/Http/Controllers/PurchaseOrderController.php:38
* @route '/purchase-orders/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::create
* @see app/Http/Controllers/PurchaseOrderController.php:38
* @route '/purchase-orders/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::create
* @see app/Http/Controllers/PurchaseOrderController.php:38
* @route '/purchase-orders/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\PurchaseOrderController::store
* @see app/Http/Controllers/PurchaseOrderController.php:50
* @route '/purchase-orders'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/purchase-orders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PurchaseOrderController::store
* @see app/Http/Controllers/PurchaseOrderController.php:50
* @route '/purchase-orders'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PurchaseOrderController::store
* @see app/Http/Controllers/PurchaseOrderController.php:50
* @route '/purchase-orders'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::store
* @see app/Http/Controllers/PurchaseOrderController.php:50
* @route '/purchase-orders'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::store
* @see app/Http/Controllers/PurchaseOrderController.php:50
* @route '/purchase-orders'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\PurchaseOrderController::show
* @see app/Http/Controllers/PurchaseOrderController.php:82
* @route '/purchase-orders/{purchase_order}'
*/
export const show = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/purchase-orders/{purchase_order}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PurchaseOrderController::show
* @see app/Http/Controllers/PurchaseOrderController.php:82
* @route '/purchase-orders/{purchase_order}'
*/
show.url = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { purchase_order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { purchase_order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            purchase_order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        purchase_order: typeof args.purchase_order === 'object'
        ? args.purchase_order.id
        : args.purchase_order,
    }

    return show.definition.url
            .replace('{purchase_order}', parsedArgs.purchase_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PurchaseOrderController::show
* @see app/Http/Controllers/PurchaseOrderController.php:82
* @route '/purchase-orders/{purchase_order}'
*/
show.get = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::show
* @see app/Http/Controllers/PurchaseOrderController.php:82
* @route '/purchase-orders/{purchase_order}'
*/
show.head = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::show
* @see app/Http/Controllers/PurchaseOrderController.php:82
* @route '/purchase-orders/{purchase_order}'
*/
const showForm = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::show
* @see app/Http/Controllers/PurchaseOrderController.php:82
* @route '/purchase-orders/{purchase_order}'
*/
showForm.get = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::show
* @see app/Http/Controllers/PurchaseOrderController.php:82
* @route '/purchase-orders/{purchase_order}'
*/
showForm.head = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\PurchaseOrderController::update
* @see app/Http/Controllers/PurchaseOrderController.php:92
* @route '/purchase-orders/{purchase_order}'
*/
export const update = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/purchase-orders/{purchase_order}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\PurchaseOrderController::update
* @see app/Http/Controllers/PurchaseOrderController.php:92
* @route '/purchase-orders/{purchase_order}'
*/
update.url = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { purchase_order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { purchase_order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            purchase_order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        purchase_order: typeof args.purchase_order === 'object'
        ? args.purchase_order.id
        : args.purchase_order,
    }

    return update.definition.url
            .replace('{purchase_order}', parsedArgs.purchase_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PurchaseOrderController::update
* @see app/Http/Controllers/PurchaseOrderController.php:92
* @route '/purchase-orders/{purchase_order}'
*/
update.put = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::update
* @see app/Http/Controllers/PurchaseOrderController.php:92
* @route '/purchase-orders/{purchase_order}'
*/
update.patch = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::update
* @see app/Http/Controllers/PurchaseOrderController.php:92
* @route '/purchase-orders/{purchase_order}'
*/
const updateForm = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::update
* @see app/Http/Controllers/PurchaseOrderController.php:92
* @route '/purchase-orders/{purchase_order}'
*/
updateForm.put = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::update
* @see app/Http/Controllers/PurchaseOrderController.php:92
* @route '/purchase-orders/{purchase_order}'
*/
updateForm.patch = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\PurchaseOrderController::receive
* @see app/Http/Controllers/PurchaseOrderController.php:107
* @route '/purchase-orders/{purchase_order}/receive'
*/
export const receive = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: receive.url(args, options),
    method: 'post',
})

receive.definition = {
    methods: ["post"],
    url: '/purchase-orders/{purchase_order}/receive',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PurchaseOrderController::receive
* @see app/Http/Controllers/PurchaseOrderController.php:107
* @route '/purchase-orders/{purchase_order}/receive'
*/
receive.url = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { purchase_order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { purchase_order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            purchase_order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        purchase_order: typeof args.purchase_order === 'object'
        ? args.purchase_order.id
        : args.purchase_order,
    }

    return receive.definition.url
            .replace('{purchase_order}', parsedArgs.purchase_order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PurchaseOrderController::receive
* @see app/Http/Controllers/PurchaseOrderController.php:107
* @route '/purchase-orders/{purchase_order}/receive'
*/
receive.post = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: receive.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::receive
* @see app/Http/Controllers/PurchaseOrderController.php:107
* @route '/purchase-orders/{purchase_order}/receive'
*/
const receiveForm = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: receive.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PurchaseOrderController::receive
* @see app/Http/Controllers/PurchaseOrderController.php:107
* @route '/purchase-orders/{purchase_order}/receive'
*/
receiveForm.post = (args: { purchase_order: number | { id: number } } | [purchase_order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: receive.url(args, options),
    method: 'post',
})

receive.form = receiveForm

const PurchaseOrderController = { index, create, store, show, update, receive }

export default PurchaseOrderController