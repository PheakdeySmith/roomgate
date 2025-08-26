<table class="table table-sm table-borderless">
    <tbody>
       <tr>
           <td class="fw-medium">Subtotal</td>
           {{-- Add the "subtotal-display" class --}}
           <td class="text-end subtotal-display">{!! format_money(0) !!}</td>
       </tr>
       <tr>
           <td class="fw-medium">Discount (%)</td>
           <td class="text-end">
                {{-- Add the "discount-input" class --}}
               <input type="number" class="form-control form-control-sm d-inline-block text-end discount-input" style="width: 80px;" value="0" min="0">
           </td>
       </tr>
        <tr>
            <td class="fw-medium">Discount Amount</td>
            {{-- Add the "discount-display" class --}}
            <td class="text-end discount-display">-{!! format_money(0) !!}</td>
        </tr>
       <tr class="fs-15 border-top border-2 border-dark">
           <th class="fw-bold">Total Amount</th>
           {{-- Add the "total-display" class --}}
           <th class="text-end total-display">{!! format_money(0) !!}</th>
       </tr>
    </tbody>
</table>