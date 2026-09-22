import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/catalog/discounts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::store
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const discounts = {
    store: Object.assign(store, store),
}

export default discounts