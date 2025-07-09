<nav class="bg-white border-gray-200 dark:bg-gray-900">
    <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
        {{-- Logo --}}
        <x-application-logo/>

        {{-- Link de contacto--}}
        <div class="flex items-center space-x-6 p-4 rtl:space-x-reverse">
            <a href="https://wa.me/584261369812" target="_blank" class="flex items-center space-x-2">
                <img src="{{ asset('assets/images/redes/whatsapp.svg') }}" alt="WhatsApp" class="w-6 h-6 inline-block">
                <p>Whatsapp</p>
            </a>
                
            <a href="{{ route('login') }}" class="text-sm  text-blue-600 dark:text-blue-500 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
            </a>
        </div>
        
    </div>
</nav>

<nav class="bg-gray-50 dark:bg-gray-700">
    <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl px-4">
        <div class="flex items-center">
            <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                <li>
                    <a href="#" class="text-gray-900 dark:text-white hover:underline" aria-current="page">Inicio</a>
                </li>
                <li>
                    <a href="#marcas" class="text-gray-900 dark:text-white hover:underline">Marcas</a>
                </li>
                <li>
                    <a href="#" class="text-gray-900 dark:text-white hover:underline">Productos</a>
                </li>
                <li>
                    <a href="#" class="text-gray-900 dark:text-white hover:underline">Categorias</a>
                </li>
            </ul>
        </div>
                    {{-- Redes Sociales --}}
            <div class="flex items-center space-x-6 rtl:space-x-reverse">

                {{-- Facebook --}}
                <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full">
                    <a href="https://www.facebook.com/share/1HpjuEnSX2/" target="_blank">
                        <img src="{{ asset('assets/images/redes/facebook.svg') }}" alt="Facebook" class="w-6 h-6 inline-block">
                    </a>
                </div>
                
                {{-- instagram --}}
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
            </div>
    </div>
</nav>
