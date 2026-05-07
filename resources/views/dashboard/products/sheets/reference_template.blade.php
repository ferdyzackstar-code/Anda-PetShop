<table>
    <thead>
        <tr>
            <th
                style="background-color:#4F81BD; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #000; padding:8px; width:100px;">
                ID KATEGORI
            </th>
            <th
                style="background-color:#4F81BD; color:#ffffff; font-weight:bold; text-align:center; border:1px solid #000; padding:8px; width:200px;">
                NAMA KATEGORI
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $species)
            <tr>
                <td
                    style="border:1px solid #000; text-align:center; font-weight:bold; background-color:#DCE6F1; padding:8px;">
                    {{ $species->id }}
                </td>
                <td style="border:1px solid #000; padding:8px; font-weight:bold; background-color:#DCE6F1;">
                    — {{ $species->name }}
                </td>
            </tr>

            @foreach ($species->children as $category)
                <tr>
                    <td style="border:1px solid #000; text-align:center; padding:8px;">
                        {{ $category->id }}
                    </td>
                    <td style="border:1px solid #000; padding:8px 8px 8px 20px; color:#595959; font-style:italic;">
                        {{ $category->name }}
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
