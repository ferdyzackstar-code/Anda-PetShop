<table>
    <thead>
        <tr>
            <th colspan="2"
                style="background-color: #C0504D; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000;">
                ID KATEGORI</th>
            <th></th>
            <th colspan="2"
                style="background-color: #9BBB59; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000;">
                ID SUPPLIER</th>
            <th></th>
            <th colspan="2"
                style="background-color: #8064A2; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000;">
                ID OUTLET</th>
        </tr>
    </thead>
    <tbody>
        @php
            $maxRows = max($categories->count(), $suppliers->count(), $outlets->count());
        @endphp

        @for ($i = 0; $i < $maxRows; $i++)
            <tr> 
                @php $category = $categories[$i] ?? null; @endphp

                <td
                    style="border: 1px solid #000000; text-align: center; {{ $category && is_null($category->parent_id) ? 'font-weight: bold; background-color: #F2DCDB;' : '' }}">
                    {{ $category->id ?? '' }}
                </td>

                <td
                    style="border: 1px solid #000000; 

            {{ $category && !is_null($category->parent_id) ? 'padding-left: 20px; color: #595959; font-style: italic;' : 'font-weight: bold;' }}">

                    @if ($category) 
                        {{ is_null($category->parent_id) ? '— ' . $category->name : $category->name }}
                    @endif
                </td>

                <td></td>

                <td style="border: 1px solid #000000; text-align: center;">{{ $suppliers[$i]->id ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ $suppliers[$i]->name ?? '' }}</td>

                <td></td>

                <td style="border: 1px solid #000000; text-align: center;">{{ $outlets[$i]->id ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ $outlets[$i]->name ?? '' }}</td>
            </tr>
        @endfor
    </tbody>
</table>
