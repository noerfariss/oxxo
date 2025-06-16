import { SearchOutlined, TagsTwoTone } from '@ant-design/icons'
import { Col, Input, Row } from 'antd'
import React, { useEffect, useState } from 'react'
import { Colors } from '../utils/Colors'

export default function Category({ datas, onChangeSearch, onChangeCategory, searchCategory }) {
    return (
        <Row gutter={12} align={'middle'} style={{ marginBottom: 12 }}>
            <Col xs={24} sm={9}>
                <Input placeholder='Cari produk' prefix={<SearchOutlined />} onChange={(e) => onChangeSearch(e.target.value)} />
            </Col>
            <Col xs={24} sm={15}>
                <div style={{ overflowX: 'auto', whiteSpace: 'nowrap' }}>
                    <div style={{ display: 'flex', gap: 12, width: 'max-content' }}>
                        <button type='button' onClick={() => onChangeCategory('')}
                            style={{
                                borderWidth:0,
                                backgroundColor: Colors.primary,
                                color: 'white',
                                paddingTop: 4,
                                paddingBottom: 4,
                                paddingLeft: 12,
                                paddingRight: 12,
                                borderRadius: 6,
                                textTransform: 'uppercase',
                                cursor: 'pointer',
                                flexShrink: 0
                            }}>
                            <TagsTwoTone /> Semua
                        </button>

                        {
                            datas.length > 0 &&
                            datas.map((val, i) => (
                                <button key={i} type='button' onClick={() => onChangeCategory(val.id)}
                                    style={{
                                        borderWidth: searchCategory == val.id ? 4 : 0,
                                        borderColor: searchCategory == val.id ? Colors.yellow : '',
                                        borderStyle: searchCategory == val.id ? 'solid' : '',
                                        backgroundColor: Colors.primary,
                                        color: 'white',
                                        paddingTop: 4,
                                        paddingBottom: 4,
                                        paddingLeft: 12,
                                        paddingRight: 12,
                                        borderRadius: 6,
                                        textTransform: 'uppercase',
                                        cursor: 'pointer',
                                        flexShrink: 0
                                    }}>
                                    <TagsTwoTone /> {val.name}
                                </button>
                            ))
                        }
                    </div>
                </div>

            </Col>
        </Row>
    )
}
