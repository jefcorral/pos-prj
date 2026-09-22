import DashboardController from './DashboardController'
import PosController from './PosController'
import ProductController from './ProductController'
import CatalogController from './CatalogController'
import InventoryController from './InventoryController'
import SaleController from './SaleController'
import ShiftController from './ShiftController'
import CustomerController from './CustomerController'
import SupplierController from './SupplierController'
import PurchaseOrderController from './PurchaseOrderController'
import ReportController from './ReportController'
import UserController from './UserController'
import BranchController from './BranchController'
import Settings from './Settings'

const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    PosController: Object.assign(PosController, PosController),
    ProductController: Object.assign(ProductController, ProductController),
    CatalogController: Object.assign(CatalogController, CatalogController),
    InventoryController: Object.assign(InventoryController, InventoryController),
    SaleController: Object.assign(SaleController, SaleController),
    ShiftController: Object.assign(ShiftController, ShiftController),
    CustomerController: Object.assign(CustomerController, CustomerController),
    SupplierController: Object.assign(SupplierController, SupplierController),
    PurchaseOrderController: Object.assign(PurchaseOrderController, PurchaseOrderController),
    ReportController: Object.assign(ReportController, ReportController),
    UserController: Object.assign(UserController, UserController),
    BranchController: Object.assign(BranchController, BranchController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers