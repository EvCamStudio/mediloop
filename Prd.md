# MediLoop — Product Requirements Document (PRD)

## 1. Executive Summary & Product Positioning
- **Product Name**: MediLoop
- **Positioning**: Clinical and logistical decision-support platform for redistributing surplus, near-expiry pharmaceuticals between healthcare facilities.
- **Critical Principles**:
  - **Decision-support only, NOT an open marketplace**: Algorithms evaluate and recommend rebalance opportunities; licensed human personnel (Pharmacists, Heads of Pharmacy, Health Department officials) must review and approve every transaction.
  - **Zero Patient Data**: No PHI (Protected Health Information) is collected, stored, or processed. Tracking is strictly at batch, SKU, facility, and aggregate consumption levels.
  - **No Medical Dosage Advice**: The system coordinates inventory and logistics viability, never clinical patient treatment plans.
  - **Regulatory Compliance**: Designed to align with Indonesian Ministry of Health (Kemenkes) & BPOM distribution and donation standards.

---

## 2. Target User Roles & Permissions Matrix
| Role | Scope | Key Capabilities |
| :--- | :--- | :--- |
| **Donor Staff / Pharmacist** | Facility-level | Log batches, view impending expiry alerts, initiate transfer proposals |
| **Donor Approver (Head of Pharmacy)** | Facility-level | Formal authorization of outgoing redistribution proposals |
| **Recipient Staff / Pharmacist** | Facility-level | Log local stockouts & consumption rates, inspect incoming transfer proposals |
| **Recipient Approver (Facility Director)** | Facility-level | Accept incoming medicine transfers and confirm physical arrival |
| **Dinkes / Health Agency Auditor** | Regional-level | View-only analytics, regional rebalance oversight, audit trail verification |
| **System Administrator** | Platform-level | Facility verification, master medicine catalogue management, token configs |

---

## 3. Mathematical Formulas & Algorithmic Specifications (Section 9)

### 3.1 Consumption & Expiry Dynamics
1. **Daily Burn Rate ($B$)**:
   $$B = \frac{\text{Total Units Dispensed in Window } W}{\text{Days in Window } W}$$
   *(Default observation window $W = 30$ or $90$ days)*

2. **Days to Deplete ($D_{\text{deplete}}$)**:
   $$D_{\text{deplete}} = \begin{cases} \frac{\text{Current Stock } (S)}{B} & \text{if } B > 0 \\ \infty & \text{if } B = 0 \end{cases}$$

3. **Days to Expiry ($D_{\text{expiry}}$)**:
   $$D_{\text{expiry}} = \text{Expiry Date} - \text{Current Date (Days)}$$

4. **Projected Waste ($W_{\text{projected}}$)**:
   $$W_{\text{projected}} = \max\left(0, S - (B \times D_{\text{expiry}})\right)$$

5. **Risk Level Thresholds**:
   - **Critical (Red/Risk)**: $D_{\text{expiry}} \le 30$ days OR Projected Waste $\ge 50\%$ of stock ($D_{\text{deplete}} > 2 \times D_{\text{expiry}}$)
   - **High (Orange/Risk)**: $D_{\text{expiry}} \le 60$ days OR Projected Waste $\ge 25\%$ of stock
   - **Medium (Amber/Warning)**: $D_{\text{expiry}} \le 90$ days OR Projected Waste $> 0$
   - **Low**: $D_{\text{deplete}} \le D_{\text{expiry}}$ (Projected Waste $= 0$)

> **Reference Benchmark Test Case**:
> - Current Stock ($S$) = 480 units
> - Burn Rate ($B$) = 4 units/day
> - Days to Expiry ($D_{\text{expiry}}$) = 45 days
> - Projected Consumption before expiry = $4 \times 45 = 180$ units
> - Days to Deplete ($D_{\text{deplete}}$) = $480 / 4 = 120$ days
> - Projected Waste ($W_{\text{projected}}$) = $480 - 180 = 300$ units
> - Waste Ratio = $300 / 480 = 62.5\%$
> - **Resulting Risk Classification**: **High** ($62.5\% \ge 25\%$ with $D_{\text{expiry}} \le 60$ days).

---

### 3.2 Candidate Eligibility & Matching Score (Section 9.4)
When donor surplus is detected, recipient candidates are scored based on 3 weighted components:

$$\text{Match Score} = (0.50 \times S_{\text{need}}) + (0.30 \times S_{\text{proximity}}) + (0.20 \times S_{\text{logistics}})$$

1. **Need Score ($S_{\text{need}}$, 50%)**:
   Calculated from stockout risk, recipient daily burn rate, and projected shortage within expiry window.
2. **Proximity Score ($S_{\text{proximity}}$, 30%)**:
   Calculated using the **Haversine Distance** formula between donor and recipient coordinates:
   $$d = 2R \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta\lambda}{2}\right)}\right)$$
   Score decays smoothly from 100 (adjacent facility) to 0 (beyond maximum cold-chain or transport threshold).
3. **Logistics & Cold-Chain Score ($S_{\text{logistics}}$, 20%)**:
   Considers cold-chain equipment readiness, licensed pharmacist availability, and past transfer completion rates.
4. **Recipient Transfer Intake Cap**:
   $$\text{Max Transfer Quantity} = \min\left(W_{\text{projected}}, B_{\text{recipient}} \times (D_{\text{expiry}} - T_{\text{buffer}})\right)$$
   *Prevents transferring surplus to a facility that would merely expire it secondarily.*

---

## 4. Transfer State Machine & Invariant (Section 6)
```
[ Proposed ] ──> [ Approved by Donor ] ──> [ Accepted by Recipient ] ──> [ In Transit ] ──> [ Completed ]
      │                   │                         │                           │
      └───> [ Cancelled ] └───> [ Cancelled ]       └───> [ Rejected ]          └───> [ Dispute/Returned ]
```

### Core Invariant:
> **INVARIANT: Physical and ledger inventory balances NEVER change during Proposed, Approved, Accepted, or In Transit states.**
> **Inventory reduction from Donor and addition to Recipient occurs ATOMICALLY ONLY upon the `Completed` state confirmation by the Recipient.**

---

## 5. Seed Scenarios & Regional Validation (Section 15)
- **Scenario 1 (RSUD to Puskesmas)**: RSUD has surplus Ceftriaxone injection batches expiring in 50 days; nearby Puskesmas has acute stockout due to delivery delays.
- **Scenario 2 (Private Hospital to Rural Clinic)**: Insulin vials with cold-chain monitoring transferred within 15 km radius.
- **Scenario 3 (Disaster / Emergency Outbreak Rebalance)**: Oral rehydration salts and antibiotics shifted across district clusters.

---

## 6. Design System Tokens & UI Rules (Section 10.1)
- **Design Tokens**:
  - Primary Action / Brand: `#1D4ED8` (Tailwind Blue 700)
  - Navy Headline / Surface: `#0B1F4B`
  - Sky Highlight / Neutral Soft: `#EAF1FF`
  - Structural Border: `#DBE5F5`
  - Muted Text / Secondary: `#52627F`
  - Risk Alert (Urgent): `#D9480F` (Dark Orange / Vermilion)
  - Warning Alert (Low Stock): `#B7791F` (Dark Amber)
  - **Forbidden**: Green is NOT used for standard positive indicators.
  - **Token Rule**: Orange reserved strictly for risk/waste; Amber reserved strictly for low stock.
- **Accessibility & Copy**:
  - Risk MUST be communicated with explicit text badges (e.g. `[HIGH RISK: 300 units]`), never color alone.
  - Recommendation cards must display the 3 sub-scores (Need 50%, Distance 30%, Logistics 20%).
  - Mandatory regulatory disclaimer banner on all transfer authorization views.
