# Retriever v0

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


Input:
```ts
retrieve(question: string): RetrievedSource[]
```

Algorithm:
1. lowercase normalize;
2. tokenize;
3. minimal stopwords;
4. title hit weight > body hit;
5. phrase bonus;
6. top 3–5;
7. minimum threshold.

Type:
```ts
type RetrievedSource = {
  sourceId:string;
  title:string;
  url?:string;
  content:string;
  score:number;
};
```

Jika semua skor buruk, hasil harus kosong agar sistem bisa `no_evidence`.
