<div class="relative w-full" id="chatgpt-agent-window" style="{{ $winWidth }}">
    <div class="fixed z-30 cursor-pointer" style="bottom: 1rem; right: 1rem;">
        <x-filament::button wire:click="togglePanel" id="btn-chat" :icon="$buttonIcon" :color="$panelHidden ? 'primary' : 'gray'">
            {{ $panelHidden ? $buttonText : __('chatgpt-agent::translations.close') }}
        </x-filament::button>
    </div>

    <x-filament::section
        class="flex-1 p-2 sm:p-6 justify-between flex flex-col max-h-screen fixed {{ $winPosition == 'left' ? 'left-0' : 'right-0' }} bottom-0 bg-white shadow z-30 {{ $panelHidden ? 'hidden' : '' }}"
        style="{{ $winWidth }}" id="chat-window">
        <x-slot name="heading" :icon="$buttonIcon" icon-size="md">
            {{ $name }}
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::icon-button color="gray" icon="heroicon-o-document" wire:click="resetSession()"
                label="{{ __('chatgpt-agent::translations.new_session') }}"
                tooltip="{{ __('chatgpt-agent::translations.new_session') }}" />
            <x-filament::icon-button color="gray" :icon="$winWidth != 'width:100%;' ? 'heroicon-m-arrows-pointing-out' : 'heroicon-m-arrows-pointing-in'" wire:click="changeWinWidth()"
                label="{{ __('chatgpt-agent::translations.toggle_full_screen') }}"
                tooltip="{{ __('chatgpt-agent::translations.toggle_full_screen') }}" />
            @if ($showPositionBtn)
                <x-filament::icon-button color="gray" icon="heroicon-s-arrows-right-left"
                    wire:click="changeWinPosition()" label="{{ __('chatgpt-agent::translations.move_window') }}"
                    tooltip="{{ __('chatgpt-agent::translations.move_window') }}" />
            @endif
            <x-filament::icon-button color="gray" icon="heroicon-s-minus-small" wire:click="togglePanel"
                label="{{ __('chatgpt-agent::translations.hide_chat') }}"
                tooltip="{{ __('chatgpt-agent::translations.hide_chat') }}" />
        </x-slot>

        <div id="messages"
            style="overflow: auto;
                    min-height: max(20rem, 30vh);
                    max-height: calc(100vh - 11rem);
                    padding-bottom: 1rem;
                    margin-bottom: 65px;"
            class="flex flex-col space-y-4 overflow-y-auto scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
            @foreach ($messages as $message)
                @if ($message['role'] !== 'system')
                    @if ($message['role'] == 'assistant')
                        <div class="chat-message">
                            <div class="flex items-end">
                                <div class="flex flex-col space-y-2 text-xs mx-2 order-2 items-start">
                                    <div>
                                        <div
                                            class="px-4 py-2 rounded-lg block rounded-bl-none bg-gray-300 text-gray-600">
                                            @isset($message['content'])
                                                {!! \Illuminate\Mail\Markdown::parse($message['content']) !!}
                                            @endisset
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="relative h-5 w-5 p-1 rounded-full text-white flex items-center justify-center bg-primary-500">
                                    <x-chatgpt-agent::chatgpt-svg />
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="chat-message">
                            <div class="flex items-end justify-end">
                                <div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end">
                                    <div>
                                        <div class="px-4 py-2 rounded-lg block rounded-br-none bg-blue-600 text-white">
                                            {!! \Illuminate\Mail\Markdown::parse($message['content']) !!}
                                        </div>
                                    </div>
                                </div>
                                <x-filament::avatar size="sm" :src="auth()->user()->getFilamentAvatarUrl()" />
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
        <div
            class="fi-section-footer border-t border-gray-200 pt-4 dark:border-white/10 absolute bottom-0 left-0 p-2 sm:p-6 bg-white dark:bg-gray-900 w-full">
            <div class="relative">
                <div
                    class="flex flex-col w-full py-2 flex-grow md:py-3 md:pl-4 relative bg-gray-200 dark:border-gray-900/50 dark:text-white dark:bg-gray-700 rounded-md shadow">
                    <textarea wire:loading.attr="disabled" wire:target="sendMessage"
                        @keydown.enter="!$event.shiftKey && ($event.preventDefault(), $wire.sendMessage())" wire:model.defer="question"
                        tabindex="0" data-id="root" style="max-height: 200px; height: 24px; padding-right:40px;"
                        placeholder="{{ __('chatgpt-agent::translations.send_a_message') }}" autofocus
                        class="m-0 w-full resize-none border-0 bg-transparent p-0 pr-7 focus:ring-0 focus:outline-none focus:placeholder-gray-400 dark:bg-transparent pl-2 md:pl-0"
                        id="chat-input"></textarea>
                    <div class="absolute bottom-1.5 md:bottom-2.5 right-1 md:right-2">
                        <x-filament::icon-button color="gray" icon="heroicon-o-paper-airplane" wire:loading.remove
                            wire:target="sendMessage" wire:click="sendMessage"
                            label="{{ __('chatgpt-agent::translations.send_message') }}" />
                        <x-filament::loading-indicator wire:loading wire:target="sendMessage" size="md"
                            wire:target="sendMessage" />
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    <style>
        .scrollbar-w-2::-webkit-scrollbar {
            width: 0.5rem;
            height: 0.5rem;
        }

        .scrollbar-track-blue-lighter::-webkit-scrollbar-track {
            --bg-opacity: 1;
            background-color: #f7fafc;
            background-color: rgba(247, 250, 252, var(--bg-opacity));
        }

        .scrollbar-thumb-blue::-webkit-scrollbar-thumb {
            --bg-opacity: 1;
            background-color: #edf2f7;
            background-color: rgba(237, 242, 247, var(--bg-opacity));
        }

        .scrollbar-thumb-rounded::-webkit-scrollbar-thumb {
            border-radius: 0.25rem;
        }

        /* classes did not compile in Filamentphp  */
        .bg-blue-600 {
            --tw-bg-opacity: 1;
            background-color: rgb(37 99 235 / var(--tw-bg-opacity));
        }

        .order-2 {
            order: 2;
        }

        .mx-2 {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        .mr-3 {
            margin-left: 0.75rem;
        }

        .border-0 {
            border-width: 0px;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .border-t-2 {
            border-top-width: 2px;
        }

        .border-2 {
            border-width: 2px;
        }

        .rounded-br-right {
            border-bottom-right-radius: 0px;
        }

        .rounded-sm {
            border-raidus: 0.125rem;
        }

        .p-1 {
            padding: 0.25rem;
        }

        .pl-1 {
            padding-left: 0.25rem;
        }

        .pl-2 {
            padding-left: 0.5rem;
        }

        .pt-4 {
            padding-top: 1rem;
        }

        .h-\[30px\] {
            height: 30px;
        }

        .w-\[30px\] {
            width: 30px;
        }

        .right-0 {
            right: 0;
        }

        .left-0 {
            lef: 0;
        }

        .right-1 {
            right: 0.25rem;
        }

        .md\:right-2 {
            right: 0.5rem
        }

        .max-h-screen {
            max-height: 100vh;
        }
    </style>

    <script>
        const el = document.getElementById('messages');
        var textarea = document.querySelector('#chat-input');

        window.onload = function() {
            setTimeout(() => {
                el.scrollTop = el.scrollHeight;
                textarea.focus();
                el.style.paddingBottom = `${textarea.scrollHeight}px`;
            }, 100)
        }


        textarea.addEventListener("input", function(e) {
            this.style.height = "inherit";
            this.style.height = `${this.scrollHeight}px`;
            el.style.paddingBottom = `${this.scrollHeight}px`;
            el.scrollTop = el.scrollHeight;
        });

        window.addEventListener('sendmessage', event => {
            setTimeout(() => {
                el.scrollTop = el.scrollHeight
            }, 100)
        })
    </script>

</div>
