export function getCartItemId(productId: string, attributes: Record<string, string>) {
    return productId + '-' + Object.entries(attributes).sort().map(([k, v]) => `${k}:${v}`).join('|');
}