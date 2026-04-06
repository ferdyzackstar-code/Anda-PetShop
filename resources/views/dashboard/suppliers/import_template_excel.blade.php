<table>
    <thead>
        <tr>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                name</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                email</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                city</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                phone</th>
            <th
                style="background-color: #4F81BD; color: #ffffff; border: 1px solid #000000; text-align: center; font-weight: bold;">
                address</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 1; $i <= 2; $i++)
            <tr>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Nama Supplier Ke-{{ $i }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Email Supplier Ke-{{ $i }}@gmail.com</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Kota Supplier Ke-{{ $i }}</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    No Telepon Supplier Ke-{{ $i }}</td>
                <td
                    style="border: 1px solid #000000; background-color: {{ $i % 2 == 0 ? '#FFFFFF' : '#DCE6F1' }};">
                    Alamat Supplier Ke-{{ $i }}</td>
            </tr>
        @endfor
    </tbody>
</table>
