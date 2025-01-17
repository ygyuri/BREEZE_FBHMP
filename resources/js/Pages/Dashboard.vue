<script setup>
import SidebarMenu from '@/Layouts/SidebarMenu.vue';
import { Layout } from 'ant-design-vue';
</script>

<template>
    <AuthenticatedLayout>
        <Layout style="min-height: 100vh">
            <!-- Sidebar -->
            <SidebarMenu />

            <!-- Main Content Area -->
            <Layout>
                <!-- Top Header -->
                <Layout.Header
                    style="
                        background: white;
                        padding: 0 24px;
                        display: flex;
                        align-items: center;
                    "
                >
                    <h2 class="text-xl font-semibold">Admin Dashboard</h2>
                </Layout.Header>

                <!-- Content -->
                <Layout.Content
                    style="
                        margin: 24px;
                        padding: 24px;
                        background: #fff;
                        min-height: 360px;
                    "
                >
                    <router-view />
                </Layout.Content>

                <!-- Footer -->
                <Layout.Footer style="text-align: center; background: white">
                    Admin Dashboard ©2025 Created with Laravel & Ant Design Vue
                </Layout.Footer>
            </Layout>
        </Layout>
    </AuthenticatedLayout>
</template>
