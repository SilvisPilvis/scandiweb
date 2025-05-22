type Product {
  id: String!
  name: String!
  inStock: Boolean!
  gallery: [String!]!
  description: String!
  category: Category!
  attributes: [AttributeSet!]!
  prices: [Price!]!
  brand: String!
}

type Query {
  products: [Product!]!
}

type Mutation {
  createProduct(product: ProductInput!): Product!
}
