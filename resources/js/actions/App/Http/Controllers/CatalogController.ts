import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\CatalogController::storeCategory
* @see app/Http/Controllers/CatalogController.php:36
* @route '/catalog/categories'
*/
export const storeCategory = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCategory.url(options),
    method: 'post',
})

storeCategory.definition = {
    methods: ["post"],
    url: '/catalog/categories',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::storeCategory
* @see app/Http/Controllers/CatalogController.php:36
* @route '/catalog/categories'
*/
storeCategory.url = (options?: RouteQueryOptions) => {
    return storeCategory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::storeCategory
* @see app/Http/Controllers/CatalogController.php:36
* @route '/catalog/categories'
*/
storeCategory.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCategory.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeCategory
* @see app/Http/Controllers/CatalogController.php:36
* @route '/catalog/categories'
*/
const storeCategoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeCategory.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeCategory
* @see app/Http/Controllers/CatalogController.php:36
* @route '/catalog/categories'
*/
storeCategoryForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeCategory.url(options),
    method: 'post',
})

storeCategory.form = storeCategoryForm

/**
* @see \App\Http\Controllers\CatalogController::updateCategory
* @see app/Http/Controllers/CatalogController.php:46
* @route '/catalog/categories/{category}'
*/
export const updateCategory = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCategory.url(args, options),
    method: 'put',
})

updateCategory.definition = {
    methods: ["put"],
    url: '/catalog/categories/{category}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CatalogController::updateCategory
* @see app/Http/Controllers/CatalogController.php:46
* @route '/catalog/categories/{category}'
*/
updateCategory.url = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { category: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            category: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        category: typeof args.category === 'object'
        ? args.category.id
        : args.category,
    }

    return updateCategory.definition.url
            .replace('{category}', parsedArgs.category.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::updateCategory
* @see app/Http/Controllers/CatalogController.php:46
* @route '/catalog/categories/{category}'
*/
updateCategory.put = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCategory.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\CatalogController::updateCategory
* @see app/Http/Controllers/CatalogController.php:46
* @route '/catalog/categories/{category}'
*/
const updateCategoryForm = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCategory.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::updateCategory
* @see app/Http/Controllers/CatalogController.php:46
* @route '/catalog/categories/{category}'
*/
updateCategoryForm.put = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCategory.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateCategory.form = updateCategoryForm

/**
* @see \App\Http\Controllers\CatalogController::destroyCategory
* @see app/Http/Controllers/CatalogController.php:55
* @route '/catalog/categories/{category}'
*/
export const destroyCategory = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyCategory.url(args, options),
    method: 'delete',
})

destroyCategory.definition = {
    methods: ["delete"],
    url: '/catalog/categories/{category}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CatalogController::destroyCategory
* @see app/Http/Controllers/CatalogController.php:55
* @route '/catalog/categories/{category}'
*/
destroyCategory.url = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { category: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            category: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        category: typeof args.category === 'object'
        ? args.category.id
        : args.category,
    }

    return destroyCategory.definition.url
            .replace('{category}', parsedArgs.category.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::destroyCategory
* @see app/Http/Controllers/CatalogController.php:55
* @route '/catalog/categories/{category}'
*/
destroyCategory.delete = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyCategory.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CatalogController::destroyCategory
* @see app/Http/Controllers/CatalogController.php:55
* @route '/catalog/categories/{category}'
*/
const destroyCategoryForm = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyCategory.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::destroyCategory
* @see app/Http/Controllers/CatalogController.php:55
* @route '/catalog/categories/{category}'
*/
destroyCategoryForm.delete = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyCategory.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyCategory.form = destroyCategoryForm

/**
* @see \App\Http\Controllers\CatalogController::storeBrand
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
export const storeBrand = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeBrand.url(options),
    method: 'post',
})

storeBrand.definition = {
    methods: ["post"],
    url: '/catalog/brands',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::storeBrand
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
storeBrand.url = (options?: RouteQueryOptions) => {
    return storeBrand.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::storeBrand
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
storeBrand.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeBrand.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeBrand
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
const storeBrandForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeBrand.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeBrand
* @see app/Http/Controllers/CatalogController.php:63
* @route '/catalog/brands'
*/
storeBrandForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeBrand.url(options),
    method: 'post',
})

storeBrand.form = storeBrandForm

/**
* @see \App\Http\Controllers\CatalogController::destroyBrand
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
export const destroyBrand = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyBrand.url(args, options),
    method: 'delete',
})

destroyBrand.definition = {
    methods: ["delete"],
    url: '/catalog/brands/{brand}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CatalogController::destroyBrand
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
destroyBrand.url = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroyBrand.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::destroyBrand
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
destroyBrand.delete = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyBrand.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CatalogController::destroyBrand
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
const destroyBrandForm = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyBrand.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::destroyBrand
* @see app/Http/Controllers/CatalogController.php:72
* @route '/catalog/brands/{brand}'
*/
destroyBrandForm.delete = (args: { brand: number | { id: number } } | [brand: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyBrand.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyBrand.form = destroyBrandForm

/**
* @see \App\Http\Controllers\CatalogController::storeUnit
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
export const storeUnit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeUnit.url(options),
    method: 'post',
})

storeUnit.definition = {
    methods: ["post"],
    url: '/catalog/units',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::storeUnit
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
storeUnit.url = (options?: RouteQueryOptions) => {
    return storeUnit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::storeUnit
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
storeUnit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeUnit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeUnit
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
const storeUnitForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeUnit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeUnit
* @see app/Http/Controllers/CatalogController.php:81
* @route '/catalog/units'
*/
storeUnitForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeUnit.url(options),
    method: 'post',
})

storeUnit.form = storeUnitForm

/**
* @see \App\Http\Controllers\CatalogController::storeTax
* @see app/Http/Controllers/CatalogController.php:93
* @route '/catalog/taxes'
*/
export const storeTax = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeTax.url(options),
    method: 'post',
})

storeTax.definition = {
    methods: ["post"],
    url: '/catalog/taxes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::storeTax
* @see app/Http/Controllers/CatalogController.php:93
* @route '/catalog/taxes'
*/
storeTax.url = (options?: RouteQueryOptions) => {
    return storeTax.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::storeTax
* @see app/Http/Controllers/CatalogController.php:93
* @route '/catalog/taxes'
*/
storeTax.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeTax.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeTax
* @see app/Http/Controllers/CatalogController.php:93
* @route '/catalog/taxes'
*/
const storeTaxForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeTax.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeTax
* @see app/Http/Controllers/CatalogController.php:93
* @route '/catalog/taxes'
*/
storeTaxForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeTax.url(options),
    method: 'post',
})

storeTax.form = storeTaxForm

/**
* @see \App\Http\Controllers\CatalogController::storeDiscount
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
export const storeDiscount = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeDiscount.url(options),
    method: 'post',
})

storeDiscount.definition = {
    methods: ["post"],
    url: '/catalog/discounts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CatalogController::storeDiscount
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
storeDiscount.url = (options?: RouteQueryOptions) => {
    return storeDiscount.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CatalogController::storeDiscount
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
storeDiscount.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeDiscount.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeDiscount
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
const storeDiscountForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeDiscount.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CatalogController::storeDiscount
* @see app/Http/Controllers/CatalogController.php:106
* @route '/catalog/discounts'
*/
storeDiscountForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeDiscount.url(options),
    method: 'post',
})

storeDiscount.form = storeDiscountForm

const CatalogController = { index, storeCategory, updateCategory, destroyCategory, storeBrand, destroyBrand, storeUnit, storeTax, storeDiscount }

export default CatalogController