type AttributeSet {
  id: String!
  items: [Attribute!]!
  name: String!
  type: String!
}

type Query {
  # AttributeSets: [AttributeSet!]!
  attributeSets: [AttributeSet!]!
}

type Mutation {
  createAttributeSet(attributeSet: AttributeSetInput!): AttributeSet!
}
