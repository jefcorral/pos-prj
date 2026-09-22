import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/catalog/units',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const units = {
    store: Object.assign(store, store),
}

export default units