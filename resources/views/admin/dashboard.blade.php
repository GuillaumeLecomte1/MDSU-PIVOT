<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Administration Panel</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Users Management -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h4 class="font-semibold mb-2">Users Management</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Manage user accounts and roles</p>
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                View Users →
                            </a>
                        </div>

                        <!-- Ressourceries Management -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h4 class="font-semibold mb-2">Ressourceries</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Manage ressourcerie accounts and listings</p>
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                View Ressourceries →
                            </a>
                        </div>

                        <!-- Site Settings -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h4 class="font-semibold mb-2">Site Settings</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Configure website settings and options</p>
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                Manage Settings →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 