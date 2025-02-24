import { createRouter, createWebHistory } from 'vue-router';
import AdminPanel from '../components/admin/AdminPanel.vue';
import Users from '../components/admin/Users.vue';
import Employees from '../components/admin/Employees.vue';
import Customers from '../components/admin/Customers.vue';
import Orders from '../components/admin/Orders.vue';
import Products from '../components/admin/Products.vue';
import OrderDetails from '../components/admin/OrderDetails.vue';
import SellerApplications from '../components/admin/SellerApplications.vue';
import Receipt from '../components/Receipt.vue';
import OrderHistory from '../components/Profile/OrderHistory.vue';

const routes = [
    {
        path: '/admin',
        component: AdminPanel,
        children: [
            { path: 'users', component: Users },
            { path: 'employees', component: Employees },
            { path: 'customers', component: Customers },
            { path: 'orders', component: Orders },
            { path: 'products', component: Products },
            { path: 'order-details', component: OrderDetails },
            { path: 'seller-applications', component: SellerApplications }
        ]
    },
    {
        path: '/receipt',
        name: 'Receipt',
        component: Receipt
    },
    {
        path: '/order-history',
        name: 'OrderHistory',
        component: OrderHistory,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;