@if($mission->mission_type === 'Point & Click')
    @include('misi.point_click')
@else
    @include('misi.syntax_assembly')
@endif