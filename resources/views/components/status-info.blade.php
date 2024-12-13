@props([ 'status', 'message' => null, 'color' => null])
<div class="flex flex-col gap-y-4">
    <h3>
        Your Vaccine Status is:
        <span @class(['px-3 py-2 font-medium rounded', 'text-violet-950 bg-violet-300'=> !$color, $color])>
            {{ str($status)->replace('_', ' ')->title() }}
        </span>
    </h3>
    @if ($message)
    <p>
        {{ $message }}
        <span class="font-semibold">{{ auth()->user()->scheduled_at?->toDateString() }}</span>
    </p>
    @endif
</div>
