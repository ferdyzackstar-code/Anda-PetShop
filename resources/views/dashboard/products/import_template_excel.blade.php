<table>
    <thead>
        <tr>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold; width: 200px;">
                name</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">price
            </th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">stock
            </th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">
                species_id</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">
                category_id</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">
                supplier_id</th>
            <th style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold;">
                outlet_id</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; font-weight: bold; width: 300px;">
                detail</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 1; $i <= 5; $i++)
            <tr>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Whiskas Adult {{ $i }}kg
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: right;">
                    {{ 50000 + $i * 1000 }}
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: right;">
                    {{ 10 * $i }}
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">
                    1
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">
                    1
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">
                    1
                </td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }}; text-align: center;">
                    1
                </td>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Contoh detail produk ke-{{ $i }}
                </td>
            </tr>
        @endfor
    </tbody>
</table>

<br>

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
                {{-- Kolom Kategori --}}
                <td style="border: 1px solid #000000; text-align: center;">{{ $categories[$i]->id ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ $categories[$i]->name ?? '' }}</td>

                <td></td> {{-- Spasi --}}

                {{-- Kolom Supplier --}}
                <td style="border: 1px solid #000000; text-align: center;">{{ $suppliers[$i]->id ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ $suppliers[$i]->name ?? '' }}</td>

                <td></td> {{-- Spasi --}}

                {{-- Kolom Outlet --}}
                <td style="border: 1px solid #000000; text-align: center;">{{ $outlets[$i]->id ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ $outlets[$i]->name ?? '' }}</td>
            </tr>
        @endfor
    </tbody>
</table>
