<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Poultry Farm House Rules') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 sm:p-8">
                <div class="flex flex-col gap-2 border-b border-gray-200 pb-5">
                    <p class="text-sm text-gray-500">{{ __('House rules revision') }} {{ $houseRulesRevision }}</p>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Poultry Farm House Rules') }}</h1>
                    <p class="text-sm text-gray-600">{{ __('Please read these rules before entering the poultry building.') }}</p>
                </div>

                <div class="mt-6 space-y-7 text-sm leading-6 text-gray-700">
                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">1. {{ __('General Guidelines') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Basaha ug sabta ang tanang house rules sa dili pa magsugod sa trabaho.</li>
                            <li>I-review ang house rules kada adlaw aron masiguro nga nasunod ang mga lagda.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">2. {{ __('Daily House Sanitation') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Limpyohi kanunay ang feeders ug drinkers sa mga manok.</li>
                            <li>Siguroha nga limpyo ug uga ang flooring o litter sa poultry house.</li>
                            <li>Kuhaa dayon ang mga patay nga manok ug ibutang sa gitakdang sako o sudlanan para sa husto nga disposal.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">3. {{ __('Poultry House Monitoring') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Siguroha nga adunay igo nga feeds ug limpyo nga tubig ang mga manok.</li>
                            <li>Kanunay nga limpyohi ug i-disinfect ang isolation area.</li>
                            <li>Kung adunay ilaga o timailhan sa peste, i-report dayon aron mahimo ang angay nga pest control.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">4. {{ __('Bird Health Monitoring') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Susiha ang kahimtang sa mga manok tulo (3) ka beses kada adlaw.</li>
                            <li>Bantayi ang ubo, sip-on, basa nga tae, pagdaghan sa mortality, pagkunhod sa pagkaon, ug kaluya.</li>
                            <li>I-report dayon sa technician o tag-iya kung adunay dili normal nga sintomas.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">5. {{ __('Strict Entry and Biosecurity Protocol') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Dili tugotan ang unauthorized visitors nga mosulod sa poultry building.</li>
                            <li>Kinahanglan moagi sa footbath sa dili pa mosulod.</li>
                            <li>Pagsul-ob og designated poultry house clothing ug footwear.</li>
                            <li>Hugasi ug i-sanitize ang mga kamot ug sundon ang foot hygiene procedures.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">6. {{ __('Mortality Handling') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Kuhaa dayon ang patay nga manok ug ibutang sa designated disposal container.</li>
                            <li>I-dispose ang patay nga manok sumala sa farm biosecurity procedures.</li>
                            <li>Ayaw biyai ang patay nga manok sulod sa poultry house kay mahimo kining tinubdan sa sakit.</li>
                            <li>I-record ang gidaghanon sa mortality matag adlaw.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">7. {{ __('Ventilation and Temperature Management') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Monitor ang temperatura ug ventilation sulod sa poultry building.</li>
                            <li>Siguroha nga dili sobra ka-init o kabugnaw ang palibot sa manok.</li>
                            <li>Likayi ang ammonia buildup pinaagi sa husto nga ventilation ug litter management.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">8. {{ __('Feeds and Water Management') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Siguroha nga adunay igo nga feeds ug tubig sumala sa feeding schedule.</li>
                            <li>Ayaw tugoti nga madaot o mahugawan ang feeds sa feeder.</li>
                            <li>Kung adunay molds o dili normal nga baho sa feeds, ayaw kini ipakaon ug i-report dayon.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">9. {{ __('Flockman Responsibilities') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>Likayi ang mga kalihokan nga makahatag og stress sa mga manok.</li>
                            <li>Sunda ang tanang house rules ug biosecurity protocols.</li>
                            <li>Pakan-a ug painoma ang mga manok sa hustong oras.</li>
                            <li>Dili tugotan ang pagdala o pagkaon og lutong manok sulod sa poultry building.</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="text-lg font-semibold text-gray-900">10. {{ __('Daily Record Keeping') }}</h3>
                        <ul class="mt-2 list-disc space-y-1 pl-6">
                            <li>I-record ang adlaw-adlaw nga mortality.</li>
                            <li>I-record ang feeds ug water consumption.</li>
                            <li>I-monitor ug i-record ang temperatura sa building.</li>
                            <li>I-record ang timbang sa mga manok kung adunay weighing schedule.</li>
                            <li>I-record ang medication ug vaccination activities.</li>
                            <li>I-record ang mga authorized visitors ug personnel nga misulod sa building.</li>
                        </ul>
                    </section>

                    <section class="border-l-4 border-orange-500 bg-orange-50 p-4">
                        <h3 class="font-bold text-orange-900">{{ __('IMPORTANT REMINDER') }}</h3>
                        <p class="mt-1">Kung adunay problema, sakit sa manok, o pangutana bahin sa operasyon, kontaka dayon ang farm technician o tag-iya.</p>
                    </section>
                </div>

                @if (!$hasAcknowledgedHouseRules)
                    <form method="POST" action="{{ route('house-rules.acknowledge') }}" class="mt-8 border-t border-gray-200 pt-6">
                        @csrf
                        <label for="house_rules_acknowledged" class="flex items-start">
                            <input id="house_rules_acknowledged" type="checkbox" class="mt-1 rounded border-gray-300 text-orange-600 shadow-sm" name="house_rules_acknowledged" value="1" required onchange="this.form.requestSubmit()">
                            <span class="ml-2 text-sm text-gray-600">{{ __('I have read and understood the Poultry Farm House Rules.') }}</span>
                        </label>
                        <x-input-error :messages="$errors->get('house_rules_acknowledged')" class="mt-2" />
                        <button type="submit" class="mt-5 rounded-md bg-orange-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-orange-700">
                            {{ __('Acknowledge House Rules') }}
                        </button>
                    </form>
                @else
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <p class="text-sm font-medium text-green-700">{{ __('You have already acknowledged these house rules.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>