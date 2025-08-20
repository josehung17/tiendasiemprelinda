<nav x-data="{ open: false }" class="bg-white border-gray-200 dark:bg-gray-900">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        {{-- Logo --}}
        <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
            <x-application-logo class="h-12" />
        </a>

        {{-- Botón de menú móvil --}}
        <button @click="open = !open" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
            </svg>
        </button>

        {{-- Enlaces de navegación --}}
        <div :class="{'block': open, 'hidden': !open}" class="w-full md:block md:w-auto" id="navbar-default">
            <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                <li>
                    <a href="#" class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-blue-500" aria-current="page">Inicio</a>
                </li>
                <li>
                    <a href="#marcas" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Marcas</a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Productos</a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Categorias</a>
                </li>
                 {{-- Redes Sociales, WhatsApp y Login --}}
                <li class="flex items-center justify-between w-full md:w-auto">
                    <div class="flex items-center space-x-6 rtl:space-x-reverse">
                        {{-- Facebook --}}
                        <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full">
                            <a href="https://www.facebook.com/share/1HpjuEnSX2/" target="_blank">
                                <img src="{{ asset('assets/images/redes/facebook.svg') }}" alt="Facebook" class="w-6 h-6 inline-block">
                            </a>
                        </div>
                        
                        {{-- Instagram --}}
                        <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full">
                            <a href="https://www.instagram.com/inversionessiemprelinda?igsh=MWQ3cHE3ZnhmMW8xeQ==" target="_blank">
                                <img src="{{ asset('assets/images/redes/instagram.svg') }}" alt="Instagram" class="w-4 h-4 inline-block">
                            </a>
                        </div>

                        {{-- TikTok --}}
                        <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full">
                            <a href="https://www.tiktok.com/@inversionessiemprelinda?_t=ZM-8xtB8JFvLlw&_r=1" target="_blank">
                                <img src="{{ asset('assets/images/redes/tiktok.svg') }}" alt="TikTok" class="w-6 h-6 inline-block">
                            </a>
                        </div>

                        {{-- WhatsApp --}}
                        <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full">
                             <a href="https://wa.me/584261369812" target="_blank">
                                <img src="{{ asset('assets/images/redes/whatsapp.svg') }}" alt="WhatsApp" class="w-6 h-6 inline-block">
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="text-sm text-blue-600 dark:text-blue-500 hover:underline ml-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
