<script setup>
import { reactive, h } from 'vue';
import { Menu, Layout } from 'ant-design-vue';
import { useRouter } from 'vue-router';
import {
    DashboardOutlined,
    UserOutlined,
    BankOutlined,
    TeamOutlined,
    GiftOutlined,
} from '@ant-design/icons-vue';

// Define menu items
const menuItems = [
    {
        key: 'dashboard',
        label: 'Dashboard',
        icon: DashboardOutlined,
        route: '/dashboard',
    },
    {
        key: 'users',
        label: 'Users Management',
        icon: UserOutlined,
        route: '/users',
    },
    {
        key: 'foodbanks',
        label: 'Foodbanks',
        icon: BankOutlined,
        route: '/foodbanks',
    },
    {
        key: 'donors',
        label: 'Donors',
        icon: TeamOutlined,
        route: '/donors',
    },
    {
        key: 'recipients',
        label: 'Recipients',
        icon: GiftOutlined,
        route: '/recipients',
    },
];

// Reactive state for sidebar collapsed state
const state = reactive({
    collapsed: false,
});

// Router navigation
const router = useRouter();
function onMenuClick({ key }) {
    const menuItem = menuItems.find((item) => item.key === key);
    if (menuItem) {
        router.push(menuItem.route);
    }
}
</script>

<template>
    <Layout.Sider
        collapsible
        v-model:collapsed="state.collapsed"
        style="background: #001529"
        width="250"
    >
        <!-- Logo -->
        <div
            class="logo"
            style="
                height: 64px;
                color: white;
                text-align: center;
                line-height: 64px;
                font-size: 1.2rem;
                font-weight: bold;
            "
        >
            Admin Panel
        </div>

        <!-- Menu -->
        <Menu
            theme="dark"
            mode="inline"
            :items="
                menuItems.map((item) => ({
                    key: item.key,
                    label: item.label,
                    icon: () => h(item.icon), // Render icons dynamically
                }))
            "
            @click="onMenuClick"
        />
    </Layout.Sider>
</template>
