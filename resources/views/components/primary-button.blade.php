<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-orange-alt hover:bg-orange-500 active:bg-orange-600 focus:border-orange-600 ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>