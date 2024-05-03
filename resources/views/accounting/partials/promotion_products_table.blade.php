@foreach($promotions as $product)
    <tr class="hover:bg-grey-lighter">
        <td class="w-1/12 px-3 py-2 border-b border-grey-light">{{ $product->sap_code }}</td>
        <td class="w-5/12 px-4 py-2 border-b border-grey-light">{{ $product->product_name }}</td>
        <td class="w-1/12 px-4 py-2 border-b border-grey-light">{{ $product->group_promotion_id }}</td>
        <td class="w-3/12 px-4 py-2 border-b border-grey-light">{{ $product->promotionGroup->promotion_name }}</td>
        <td class="w-2/12 px-4 py-2 border-b border-grey-light">
            <button class="btn btn-primary btn-sm btn-edit" data-product="{{ $product }}">Sửa</button>
            <button class="btn btn-danger btn-sm btn-delete" onclick="confirmDelete({{ $product->id }})">Xóa</button>
            <form id="delete-form-{{ $product->id }}" action="{{ route('promotion_products.destroy', $product->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@endforeach