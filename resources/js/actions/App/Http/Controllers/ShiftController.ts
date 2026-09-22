import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ShiftController::index
* @see app/Http/Controllers/ShiftController.php:20
* @route '/shifts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/shifts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ShiftController::index
* @see app/Http/Controllers/ShiftController.php:20
* @route '/shifts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShiftController::index
* @see app/Http/Controllers/ShiftController.php:20
* @route '/shifts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShiftController::index
* @see app/Http/Controllers/ShiftController.php:20
* @route '/shifts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ShiftController::index
* @see app/Http/Controllers/ShiftController.php:20
* @route '/shifts'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShiftController::index
* @see app/Http/Controllers/ShiftController.php:20
* @route '/shifts'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShiftController::index
* @see app/Http/Controllers/ShiftController.php:20
* @route '/shifts'
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
* @see \App\Http\Controllers\ShiftController::open
* @see app/Http/Controllers/ShiftController.php:37
* @route '/shifts'
*/
export const open = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: open.url(options),
    method: 'post',
})

open.definition = {
    methods: ["post"],
    url: '/shifts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ShiftController::open
* @see app/Http/Controllers/ShiftController.php:37
* @route '/shifts'
*/
open.url = (options?: RouteQueryOptions) => {
    return open.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShiftController::open
* @see app/Http/Controllers/ShiftController.php:37
* @route '/shifts'
*/
open.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: open.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShiftController::open
* @see app/Http/Controllers/ShiftController.php:37
* @route '/shifts'
*/
const openForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: open.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShiftController::open
* @see app/Http/Controllers/ShiftController.php:37
* @route '/shifts'
*/
openForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: open.url(options),
    method: 'post',
})

open.form = openForm

/**
* @see \App\Http\Controllers\ShiftController::close
* @see app/Http/Controllers/ShiftController.php:49
* @route '/shifts/{shift}/close'
*/
export const close = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: close.url(args, options),
    method: 'post',
})

close.definition = {
    methods: ["post"],
    url: '/shifts/{shift}/close',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ShiftController::close
* @see app/Http/Controllers/ShiftController.php:49
* @route '/shifts/{shift}/close'
*/
close.url = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { shift: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { shift: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            shift: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        shift: typeof args.shift === 'object'
        ? args.shift.id
        : args.shift,
    }

    return close.definition.url
            .replace('{shift}', parsedArgs.shift.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShiftController::close
* @see app/Http/Controllers/ShiftController.php:49
* @route '/shifts/{shift}/close'
*/
close.post = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: close.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShiftController::close
* @see app/Http/Controllers/ShiftController.php:49
* @route '/shifts/{shift}/close'
*/
const closeForm = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: close.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShiftController::close
* @see app/Http/Controllers/ShiftController.php:49
* @route '/shifts/{shift}/close'
*/
closeForm.post = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: close.url(args, options),
    method: 'post',
})

close.form = closeForm

/**
* @see \App\Http\Controllers\ShiftController::cashMovement
* @see app/Http/Controllers/ShiftController.php:61
* @route '/shifts/{shift}/cash'
*/
export const cashMovement = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cashMovement.url(args, options),
    method: 'post',
})

cashMovement.definition = {
    methods: ["post"],
    url: '/shifts/{shift}/cash',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ShiftController::cashMovement
* @see app/Http/Controllers/ShiftController.php:61
* @route '/shifts/{shift}/cash'
*/
cashMovement.url = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { shift: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { shift: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            shift: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        shift: typeof args.shift === 'object'
        ? args.shift.id
        : args.shift,
    }

    return cashMovement.definition.url
            .replace('{shift}', parsedArgs.shift.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShiftController::cashMovement
* @see app/Http/Controllers/ShiftController.php:61
* @route '/shifts/{shift}/cash'
*/
cashMovement.post = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cashMovement.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShiftController::cashMovement
* @see app/Http/Controllers/ShiftController.php:61
* @route '/shifts/{shift}/cash'
*/
const cashMovementForm = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cashMovement.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShiftController::cashMovement
* @see app/Http/Controllers/ShiftController.php:61
* @route '/shifts/{shift}/cash'
*/
cashMovementForm.post = (args: { shift: number | { id: number } } | [shift: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cashMovement.url(args, options),
    method: 'post',
})

cashMovement.form = cashMovementForm

const ShiftController = { index, open, close, cashMovement }

export default ShiftController