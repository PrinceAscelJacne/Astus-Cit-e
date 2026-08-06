<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#1668b3] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#2c4964] focus:bg-[#2c4964] active:bg-[#12548c] focus:outline-none focus:ring-2 focus:ring-[#1668b3] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
